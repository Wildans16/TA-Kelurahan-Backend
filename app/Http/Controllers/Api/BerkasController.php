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
            'jenis_berkas' => 'required|string|max:100',
            'file' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120', // 5MB max
                'mimetypes:application/pdf,image/jpeg,image/png',
            ],
        ], [
            'file.max' => 'Ukuran file maksimal 5MB',
            'file.mimes' => 'File harus berformat PDF, JPG, JPEG, atau PNG',
            'file.mimetypes' => 'Tipe file tidak valid',
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
            
            // Sanitize filename - remove special characters
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $safeName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $originalName);
            $fileName = time() . '_' . $safeName . '.' . $extension;
            
            // Validate actual file content (check magic bytes)
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file->getPathname());
            finfo_close($finfo);
            
            $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png'];
            if (!in_array($mimeType, $allowedMimes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipe file tidak valid. File telah diubah atau korup.'
                ], 422);
            }
            
            $filePath = $file->storeAs('berkas', $fileName, 'public');

            $berkas = Berkas::create([
                'permohonan_id' => $request->permohonan_id,
                'jenis_berkas' => $request->jenis_berkas,
                'nama_file' => $fileName,
                'path' => $filePath,
                'mime_type' => $mimeType,
                'size' => $file->getSize(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File berhasil diupload',
                'data' => $berkas
            ], 201);

        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('File upload failed: ' . $e->getMessage(), [
                'permohonan_id' => $request->permohonan_id,
                'user_ip' => $request->ip()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload file. Silakan coba lagi.'
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
