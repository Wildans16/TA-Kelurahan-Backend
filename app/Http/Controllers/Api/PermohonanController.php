<?php

namespace App\Http\Controllers\Api;

use App\Models\Permohonan;
use App\Http\Controllers\Controller;
use App\Models\StatusTracking;
use App\Models\Berkas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PermohonanController extends Controller
{
    public function index(Request $request)
    {
        $query = Permohonan::with(['layanan', 'statusTracking']);

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('layanan') && $request->layanan !== 'all') {
            $query->whereHas('layanan', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->layanan . '%');
            });
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nik', 'like', '%' . $request->search . '%')
                  ->orWhere('nomor_registrasi', 'like', '%' . $request->search . '%');
            });
        }

        $permohonan = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $permohonan
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'layanan_id' => 'required|exists:layanan,id',
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|size:16',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'required|string',
            'rt' => 'required|string|max:3',
            'rw' => 'required|string|max:3',
            'no_hp' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            'keperluan' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Buat permohonan dengan status awal 'baru'
            $permohonan = Permohonan::create(array_merge(
                $request->all(),
                ['status' => 'baru']
            ));

            // Buat tracking status AWAL (hanya 1 record)
            StatusTracking::create([
                'permohonan_id' => $permohonan->id,
                'status' => 'baru',
                'step' => 'Pengajuan Diterima',
                'keterangan' => 'Permohonan Anda telah diterima dan menunggu verifikasi',
                'tanggal' => now(),
                'completed' => 1,
            ]);

            // Set estimasi selesai (3 hari kerja)
            $estimasi = now()->addDays(3);
            $permohonan->update(['estimasi_selesai' => $estimasi]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permohonan berhasil dibuat',
                'data' => [
                    'nomor_registrasi' => $permohonan->nomor_registrasi,
                    'permohonan' => $permohonan->load(['layanan', 'statusTracking'])
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat permohonan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $permohonan = Permohonan::with(['layanan', 'berkas', 'statusTracking'])
            ->find($id);

        if (!$permohonan) {
            return response()->json([
                'success' => false,
                'message' => 'Permohonan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $permohonan
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:baru,proses,selesai,ditolak',
            'catatan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $permohonan = Permohonan::find($id);

        if (!$permohonan) {
            return response()->json([
                'success' => false,
                'message' => 'Permohonan tidak ditemukan'
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Simpan status lama
            $oldStatus = $permohonan->status;

            // Update status permohonan
            $permohonan->update([
                'status' => $request->status,
                'catatan' => $request->catatan
            ]);

            // HANYA insert tracking baru jika status BERUBAH
            if ($oldStatus !== $request->status) {
                StatusTracking::create([
                    'permohonan_id' => $permohonan->id,
                    'status' => $request->status,
                    'step' => $this->getStepName($request->status),
                    'keterangan' => $request->catatan ?? $this->getDefaultKeterangan($request->status),
                    'tanggal' => now(),
                    'completed' => 1,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status permohonan berhasil diupdate',
                'data' => $permohonan->load(['statusTracking'])
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Helper function untuk nama step
    private function getStepName($status)
    {
        switch ($status) {
            case 'baru':
                return 'Pengajuan Diterima';
            case 'proses':
                return 'Verifikasi Dokumen';
            case 'persetujuan':
                return 'Proses Persetujuan';
            case 'selesai':
                return 'Selesai - Siap Diambil';
            case 'ditolak':
                return 'Ditolak';
            default:
                return 'Status Diupdate';
        }
    }


    // Helper function untuk keterangan default
    private function getDefaultKeterangan($status)
    {
        switch ($status) {
            case 'baru':
                return 'Permohonan Anda telah diterima dan menunggu verifikasi';
            case 'proses':
                return 'Dokumen sedang dalam proses verifikasi dan pembuatan';
            case 'selesai':
                return 'Dokumen sudah selesai dibuat dan siap diambil di kantor kelurahan';
            case 'ditolak':
                return 'Permohonan ditolak, silakan hubungi kantor untuk informasi lebih lanjut';
            default:
                return 'Status permohonan telah diupdate';
        }
    }

    public function destroy($id)
    {
        $permohonan = Permohonan::find($id);

        if (!$permohonan) {
            return response()->json([
                'success' => false,
                'message' => 'Permohonan tidak ditemukan'
            ], 404);
        }

        $permohonan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permohonan berhasil dihapus'
        ]);
    }

    public function bulkUpdateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:permohonan,id',
            'status' => 'required|in:baru,proses,selesai,ditolak',
            'catatan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($request->ids as $id) {
                $permohonan = Permohonan::find($id);
                if ($permohonan) {
                    $permohonan->update(['status' => $request->status]);
                    
                    StatusTracking::create([
                        'permohonan_id' => $id,
                        'status' => $request->status,
                        'catatan' => $request->catatan ?? 'Bulk update status'
                    ]);
                }
            }
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil update ' . count($request->ids) . ' permohonan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal update permohonan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:permohonan,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $count = Permohonan::whereIn('id', $request->ids)->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Berhasil menghapus ' . $count . ' permohonan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus permohonan: ' . $e->getMessage()
            ], 500);
        }
    }
}
