<?php

namespace App\Http\Controllers\Api;

use App\Models\Permohonan;
use App\Http\Controllers\Controller;
use App\Models\StatusTracking;
use App\Models\Berkas;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class PermohonanController extends Controller
{
    protected $fonnteService;

    public function __construct(FonnteService $fonnteService)
    {
        $this->fonnteService = $fonnteService;
    }

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
            'nama' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'nik' => [
                'required',
                'string',
                'size:16',
                'regex:/^[0-9]{16}$/',
                'unique:permohonan,nik,NULL,id,deleted_at,NULL'
            ],
            'tempat_lahir' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'tanggal_lahir' => 'required|date|before:today|after:1900-01-01',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'required|string|max:500',
            'rt' => 'required|string|max:3|regex:/^[0-9]{1,3}$/',
            'rw' => 'required|string|max:3|regex:/^[0-9]{1,3}$/',
            'no_hp' => [
                'required',
                'string',
                'max:15',
                'regex:/^(\+62|62|0)[0-9]{9,13}$/'
            ],
            'email' => 'required|email:rfc,dns|max:255',
            'keperluan' => 'required|string|max:1000',
            'keterangan' => 'nullable|string|max:1000',
        ], [
            'nama.regex' => 'Nama hanya boleh berisi huruf dan spasi',
            'nik.size' => 'NIK harus 16 digit',
            'nik.regex' => 'NIK harus berupa angka',
            'nik.unique' => 'NIK sudah terdaftar dalam sistem',
            'tempat_lahir.regex' => 'Tempat lahir hanya boleh berisi huruf dan spasi',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini',
            'tanggal_lahir.after' => 'Tanggal lahir tidak valid',
            'rt.regex' => 'RT harus berupa angka',
            'rw.regex' => 'RW harus berupa angka',
            'no_hp.regex' => 'Format nomor HP tidak valid (contoh: 081234567890)',
            'email.email' => 'Format email tidak valid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Additional validation: Check age (min 17 years old for KTP)
        $birthDate = new \DateTime($request->tanggal_lahir);
        $today = new \DateTime('today');
        $age = $birthDate->diff($today)->y;
        
        if ($age < 17) {
            return response()->json([
                'success' => false,
                'message' => 'Usia minimal untuk mengajukan permohonan adalah 17 tahun'
            ], 422);
        }
        
        // Sanitize input data
        $data = $request->all();
        $data['nama'] = ucwords(strtolower(trim($data['nama'])));
        $data['tempat_lahir'] = ucwords(strtolower(trim($data['tempat_lahir'])));
        $data['alamat'] = trim($data['alamat']);
        $data['keperluan'] = trim($data['keperluan']);
        if (isset($data['keterangan'])) {
            $data['keterangan'] = trim($data['keterangan']);
        }

        DB::beginTransaction();
        try {
            // Buat permohonan dengan status awal 'baru'
            $permohonan = Permohonan::create(array_merge(
                $data,
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
            
            // Log successful submission
            \Log::info('Permohonan created', [
                'nomor_registrasi' => $permohonan->nomor_registrasi,
                'nik' => $permohonan->nik,
                'ip' => $request->ip()
            ]);

            // Kirim WhatsApp notifikasi untuk permohonan baru
            if (!empty($permohonan->no_hp)) {
                try {
                    // Load relasi layanan sebelum kirim pesan
                    $permohonan->load('layanan');
                    
                    $message = $this->fonnteService->templatePermohonanBaru($permohonan);
                    $result = $this->fonnteService->sendMessage($permohonan->no_hp, $message);
                    
                    if ($result['success']) {
                        \Log::info('WhatsApp notification sent for new permohonan', [
                            'nomor_registrasi' => $permohonan->nomor_registrasi,
                            'no_hp' => $permohonan->no_hp
                        ]);
                    }
                } catch (\Exception $e) {
                    // Log error tapi jangan gagalkan proses pembuatan permohonan
                    \Log::error('Failed to send WhatsApp notification for new permohonan', [
                        'permohonan_id' => $permohonan->id,
                        'nomor_registrasi' => $permohonan->nomor_registrasi,
                        'error' => $e->getMessage()
                    ]);
                }
            }

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

                // Kirim WhatsApp notifikasi jika status berubah ke "selesai"
                if ($request->status === 'selesai' && !empty($permohonan->no_hp)) {
                    try {
                        $message = $this->fonnteService->templatePermohonanSelesai($permohonan->load('layanan'));
                        $this->fonnteService->sendMessage($permohonan->no_hp, $message);
                    } catch (\Exception $e) {
                        // Log error tapi jangan gagalkan proses update
                        \Log::error('Failed to send WhatsApp notification', [
                            'permohonan_id' => $permohonan->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
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

    public function exportExcel(Request $request)
    {
        try {
            $query = Permohonan::with(['layanan']);
            
            // Filter by date range
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            }
            
            // Filter by status
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }
            
            $data = $query->orderBy('created_at', 'desc')->get();
            
            // Create Excel file
            return Excel::download(new \App\Exports\PermohonanExport($data), 
                'Laporan_Permohonan_' . date('Y-m-d_His') . '.xlsx');
                
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal export Excel: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function exportPdf(Request $request)
    {
        try {
            $query = Permohonan::with(['layanan']);
            
            // Filter by date range
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            }
            
            // Filter by status
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }
            
            $data = $query->orderBy('created_at', 'desc')->get();
            
            $pdf = Pdf::loadView('exports.permohonan-pdf', [
                'data' => $data,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status
            ]);
            
            return $pdf->download('Laporan_Permohonan_' . date('Y-m-d_His') . '.pdf');
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal export PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
