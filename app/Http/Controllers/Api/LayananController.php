<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LayananController extends Controller
{
    public function index(Request $request)
    {
        $query = Layanan::query();

        if ($request->has('kategori') && $request->kategori !== 'all') {
            $query->where('kategori', $request->kategori);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            $query->aktif();
        }

        $layanan = $query->orderBy('nama')->get();

        return response()->json([
            'success' => true,
            'data' => $layanan
        ]);
    }

    public function show($id)
    {
        $layanan = Layanan::find($id);

        if (!$layanan) {
            return response()->json([
                'success' => false,
                'message' => 'Layanan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $layanan
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kategori' => 'required|in:surat,kependudukan,keamanan,perizinan',
            'persyaratan' => 'required|array',
            'waktu_proses' => 'required|string|max:255',
            'biaya' => 'string|max:255',
            'status' => 'in:aktif,nonaktif'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $layanan = Layanan::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Layanan berhasil dibuat',
            'data' => $layanan
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $layanan = Layanan::find($id);

        if (!$layanan) {
            return response()->json([
                'success' => false,
                'message' => 'Layanan tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'string|max:255',
            'deskripsi' => 'string',
            'kategori' => 'in:surat,kependudukan,keamanan,perizinan',
            'persyaratan' => 'array',
            'waktu_proses' => 'string|max:255',
            'biaya' => 'string|max:255',
            'status' => 'in:aktif,nonaktif'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $layanan->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Layanan berhasil diupdate',
            'data' => $layanan
        ]);
    }

    public function destroy($id)
    {
        $layanan = Layanan::find($id);

        if (!$layanan) {
            return response()->json([
                'success' => false,
                'message' => 'Layanan tidak ditemukan'
            ], 404);
        }

        $layanan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Layanan berhasil dihapus'
        ]);
    }
}
