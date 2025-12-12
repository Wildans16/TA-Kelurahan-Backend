<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permohonan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StatusController extends Controller
{
    public function check(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomor_registrasi' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $permohonan = Permohonan::with(['layanan', 'statusTracking'])
                                ->where('nomor_registrasi', $request->nomor_registrasi)
                                ->first();

        if (!$permohonan) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor registrasi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $permohonan
        ]);
    }
}
