<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Berkas;
use App\Models\Permohonan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BerkasController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'permohonan_id' => 'required|exists:permohonan,id',
            'jenis_berkas' => 'required|string',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('berkas', $fileName, 'public');

            $berkas = Berkas::create([
                'permohonan_id' => $request->permohonan_id,
                'jenis_berkas' => $request->jenis_berkas,
                'nama_file' => $fileName,
                'path' => $filePath,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File berhasil diupload',
                'data' => $berkas
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $berkas = Berkas::find($id);

        if (!$berkas) {
            return response()->json([
                'success' => false,
                'message' => 'Berkas tidak ditemukan'
            ], 404);
        }

        if (!Storage::disk('public')->exists($berkas->path)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan'
            ], 404);
        }

        return Storage::disk('public')->response($berkas->path);
    }

    public function download($id)
    {
        $berkas = Berkas::find($id);

        if (!$berkas) {
            abort(404, 'Berkas tidak ditemukan');
        }

        // Path di database sekarang tanpa 'storage/' prefix (contoh: 'test-image.jpg' atau 'berkas/file.pdf')
        // Storage facade akan otomatis cek di storage/app/public/
        if (!Storage::disk('public')->exists($berkas->path)) {
            abort(404, 'File tidak ditemukan: ' . $berkas->path);
        }

        $filePath = Storage::disk('public')->path($berkas->path);

        return response()->file($filePath, [
            'Content-Type' => $berkas->mime_type,
            'Content-Disposition' => 'inline; filename="' . $berkas->nama_file . '"'
        ]);
    }

    public function destroy($id)
    {
        $berkas = Berkas::find($id);

        if (!$berkas) {
            return response()->json([
                'success' => false,
                'message' => 'Berkas tidak ditemukan'
            ], 404);
        }

        // Hapus file dari storage
        if (Storage::disk('public')->exists($berkas->path)) {
            Storage::disk('public')->delete($berkas->path);
        }

        $berkas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berkas berhasil dihapus'
        ]);
    }
}
