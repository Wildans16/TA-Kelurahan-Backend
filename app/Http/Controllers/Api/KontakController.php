<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kontak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\BalasanPesanKontak;

class KontakController extends Controller
{

    /**
     * Display a listing of messages.
     */
    public function index(Request $request)
    {
        $query = Kontak::query();

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('subjek', 'like', '%' . $request->search . '%');
            });
        }

        $messages = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));
        
        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    /**
     * Store a newly created message.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email:rfc,dns|max:255',
            'subjek' => 'required|string|max:255|min:5',
            'pesan' => 'required|string|max:2000|min:10',
        ], [
            'nama.regex' => 'Nama hanya boleh berisi huruf dan spasi',
            'email.email' => 'Format email tidak valid',
            'subjek.min' => 'Subjek minimal 5 karakter',
            'pesan.min' => 'Pesan minimal 10 karakter',
            'pesan.max' => 'Pesan maksimal 2000 karakter',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Sanitize input
        $data = [
            'nama' => ucwords(strtolower(trim($request->nama))),
            'email' => strtolower(trim($request->email)),
            'subjek' => trim($request->subjek),
            'pesan' => trim($request->pesan),
            'status' => 'baru',
        ];

        $kontak = Kontak::create($data);
        
        // Log message submission
        \Log::info('Kontak message submitted', [
            'id' => $kontak->id,
            'email' => $kontak->email,
            'ip' => $request->ip()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dikirim',
            'data' => $kontak
        ], 201);
    }

    /**
     * Display the specified message.
     */
    public function show(string $id)
    {
        $kontak = Kontak::find($id);
        
        if (!$kontak) {
            return response()->json([
                'success' => false,
                'message' => 'Pesan tidak ditemukan'
            ], 404);
        }

        // Mark as read
        if ($kontak->status === 'baru') {
            $kontak->update(['status' => 'dibaca']);
        }

        return response()->json([
            'success' => true,
            'data' => $kontak
        ]);
    }

    /**
     * Update the message status.
     */
    public function update(Request $request, string $id)
    {
        $kontak = Kontak::find($id);
        
        if (!$kontak) {
            return response()->json([
                'success' => false,
                'message' => 'Pesan tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:baru,dibaca,dibalas',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $kontak->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status pesan diperbarui',
            'data' => $kontak
        ]);
    }

    /**
     * Remove the specified message.
     */
    public function destroy(string $id)
    {
        $kontak = Kontak::find($id);
        
        if (!$kontak) {
            return response()->json([
                'success' => false,
                'message' => 'Pesan tidak ditemukan'
            ], 404);
        }

        $kontak->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dihapus'
        ]);
    }

    /**
     * Send a reply to a contact message.
     */
    public function reply(Request $request, string $id)
    {
        $kontak = Kontak::find($id);
        if (!$kontak) {
            return response()->json([
                'success' => false,
                'message' => 'Pesan tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'balasan' => 'required|string|min:10',
        ], [
            'balasan.required' => 'Balasan tidak boleh kosong',
            'balasan.min' => 'Balasan minimal 10 karakter',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cek apakah email sudah dikonfigurasi
        $resendConfigured = config('services.resend.key');
        $smtpConfigured = config('mail.mailers.smtp.username') && config('mail.mailers.smtp.password');

        if (!$resendConfigured && !$smtpConfigured) {
            \Log::warning('Email not configured', [
                'kontak_id' => $id,
                'message' => 'Resend and SMTP not configured'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Email belum dikonfigurasi. Silakan hubungi administrator untuk mengatur Resend API atau SMTP.',
                'details' => 'Email configuration missing'
            ], 503);
        }

        try {
            $emailSent = false;
            $provider = null;

            // Try SMTP first (Gmail) - More reliable for now
            if ($smtpConfigured) {
                try {
                    Mail::mailer('smtp')->send(new BalasanPesanKontak($kontak, $request->balasan));
                    
                    $emailSent = true;
                    $provider = 'SMTP (Gmail)';
                    
                    \Log::info('Reply sent via SMTP', [
                        'kontak_id' => $kontak->id,
                        'email' => $kontak->email
                    ]);

                } catch (\Exception $smtpError) {
                    \Log::warning('SMTP failed, trying Resend', [
                        'kontak_id' => $id,
                        'error' => $smtpError->getMessage()
                    ]);
                }
            }

            // Fallback to Resend if SMTP failed or not configured
            if (!$emailSent && $resendConfigured) {
                try {
                    Mail::mailer('smtp')->send(new BalasanPesanKontak($kontak, $request->balasan));
                    
                    $emailSent = true;
                    $provider = 'SMTP (Gmail)';
                    
                    \Log::info('Reply sent via SMTP', [
                        'kontak_id' => $kontak->id,
                        'email' => $kontak->email
                    ]);

                } catch (\Exception $smtpError) {
                    \Log::error('SMTP failed', [
                        'kontak_id' => $id,
                        'error' => $smtpError->getMessage()
                    ]);
                }
            }

            if ($emailSent) {
                // Update status menjadi dibalas
                $kontak->update(['status' => 'dibalas']);

                return response()->json([
                    'success' => true,
                    'message' => "Balasan berhasil dikirim via {$provider}",
                    'provider' => $provider
                ]);
            } else {
                throw new \Exception('Semua provider email gagal mengirim');
            }

        } catch (\Exception $e) {
            \Log::error('All email providers failed', [
                'kontak_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email. Silakan coba lagi atau hubungi administrator.',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
