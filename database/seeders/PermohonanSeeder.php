<?php

namespace Database\Seeders;

use App\Models\Permohonan;
use App\Models\Berkas;
use App\Models\StatusTracking;
use App\Models\Layanan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermohonanSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua layanan
        $allLayanan = Layanan::all();
        
        if ($allLayanan->isEmpty()) {
            $this->command->error('Tidak ada layanan. Jalankan LayananSeeder terlebih dahulu.');
            return;
        }

        $this->command->info('Membuat permohonan test untuk ' . $allLayanan->count() . ' layanan...');

        $statusList = ['baru', 'proses', 'selesai', 'ditolak'];
        $jenisKelaminList = ['Laki-laki', 'Perempuan'];
        
        $namaList = [
            'John Doe', 'Jane Smith', 'Ahmad Wijaya', 'Siti Nurhaliza', 'Budi Santoso',
            'Dewi Lestari', 'Rudi Hartono', 'Rina Susanti', 'Andi Saputra', 'Maya Sari',
            'Eko Prasetyo', 'Lina Marlina', 'Agus Setiawan', 'Putri Ayu', 'Hendra Kusuma'
        ];

        $tempatLahirList = [
            'Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Semarang',
            'Makassar', 'Palembang', 'Tangerang', 'Depok', 'Bekasi'
        ];

        $alamatList = [
            'Jl. Graha Indah No. 123', 'Jl. Melati No. 45', 'Jl. Mawar No. 78',
            'Jl. Kenanga No. 12', 'Jl. Anggrek No. 34', 'Jl. Dahlia No. 56',
            'Jl. Cempaka No. 89', 'Jl. Flamboyan No. 23', 'Jl. Teratai No. 67',
            'Jl. Bougenville No. 90'
        ];

        $keperluanList = [
            'Untuk keperluan administrasi kependudukan',
            'Untuk keperluan melamar pekerjaan',
            'Untuk keperluan pendaftaran sekolah',
            'Untuk keperluan pengajuan kredit',
            'Untuk keperluan administrasi perusahaan',
            'Untuk keperluan pembuatan paspor',
            'Untuk keperluan administrasi rumah sakit'
        ];

        $count = 0;

        foreach ($allLayanan as $index => $layanan) {
            DB::beginTransaction();
            try {
                // Pilih data random untuk variasi
                $randomIndex = $index % count($namaList);
                $status = $statusList[$index % count($statusList)];
                $jenisKelamin = $jenisKelaminList[$index % 2];
                
                // Generate NIK random
                $nik = '32' . str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT) . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT) . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                
                // Generate tanggal lahir random (umur 20-60 tahun)
                $tahunLahir = date('Y') - rand(20, 60);
                $bulanLahir = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
                $hariLahir = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
                $tanggalLahir = "$tahunLahir-$bulanLahir-$hariLahir";

                // Buat permohonan
                $permohonan = Permohonan::create([
                    'layanan_id' => $layanan->id,
                    'nama' => $namaList[$randomIndex],
                    'nik' => $nik,
                    'tempat_lahir' => $tempatLahirList[$randomIndex % count($tempatLahirList)],
                    'tanggal_lahir' => $tanggalLahir,
                    'jenis_kelamin' => $jenisKelamin,
                    'alamat' => $alamatList[$randomIndex % count($alamatList)],
                    'rt' => str_pad(rand(1, 15), 3, '0', STR_PAD_LEFT),
                    'rw' => str_pad(rand(1, 10), 3, '0', STR_PAD_LEFT),
                    'no_hp' => '08' . rand(1000000000, 9999999999),
                    'email' => strtolower(str_replace(' ', '', $namaList[$randomIndex])) . '@example.com',
                    'keperluan' => $keperluanList[$randomIndex % count($keperluanList)],
                    'keterangan' => $index % 3 === 0 ? 'Mohon diproses secepatnya' : null,
                    'status' => $status,
                    'estimasi_selesai' => now()->addDays(rand(2, 7)),
                ]);

                // Buat berkas dengan kombinasi PDF dan JPG
                // Berkas 1: KTP (JPG)
                Berkas::create([
                    'permohonan_id' => $permohonan->id,
                    'jenis_berkas' => 'ktp',
                    'nama_file' => 'test-image.jpg',
                    'path' => 'test-image.jpg',
                    'mime_type' => 'image/jpeg',
                    'size' => file_exists(public_path('storage/test-image.jpg')) 
                        ? filesize(public_path('storage/test-image.jpg')) 
                        : 50000,
                ]);

                // Berkas 2: KK (PDF)
                Berkas::create([
                    'permohonan_id' => $permohonan->id,
                    'jenis_berkas' => 'kk',
                    'nama_file' => 'test-document.pdf',
                    'path' => 'test-document.pdf',
                    'mime_type' => 'application/pdf',
                    'size' => filesize(public_path('storage/test-document.pdf')),
                ]);

                // Berkas tambahan untuk variasi (50% kemungkinan)
                if ($index % 2 === 0) {
                    Berkas::create([
                        'permohonan_id' => $permohonan->id,
                        'jenis_berkas' => 'lainnya',
                        'nama_file' => 'test-document.pdf',
                        'path' => 'test-document.pdf',
                        'mime_type' => 'application/pdf',
                        'size' => filesize(public_path('storage/test-document.pdf')),
                    ]);
                }

                // Buat status tracking
                StatusTracking::create([
                    'permohonan_id' => $permohonan->id,
                    'step' => 'Pengajuan Diterima',
                    'keterangan' => 'Permohonan Anda telah diterima dan menunggu verifikasi',
                    'tanggal' => now()->subDays(rand(1, 5)),
                    'completed' => 1,
                ]);

                // Tambah status tracking sesuai status permohonan
                if (in_array($status, ['proses', 'selesai'])) {
                    StatusTracking::create([
                        'permohonan_id' => $permohonan->id,
                        'step' => 'Verifikasi Berkas',
                        'keterangan' => 'Berkas sedang diverifikasi oleh petugas',
                        'tanggal' => now()->subDays(rand(0, 3)),
                        'completed' => 1,
                    ]);
                }

                if ($status === 'selesai') {
                    StatusTracking::create([
                        'permohonan_id' => $permohonan->id,
                        'step' => 'Selesai Diproses',
                        'keterangan' => 'Permohonan Anda telah selesai diproses',
                        'tanggal' => now(),
                        'completed' => 1,
                    ]);
                }

                if ($status === 'ditolak') {
                    StatusTracking::create([
                        'permohonan_id' => $permohonan->id,
                        'step' => 'Ditolak',
                        'keterangan' => 'Permohonan ditolak karena berkas tidak lengkap',
                        'tanggal' => now(),
                        'completed' => 1,
                    ]);
                }

                DB::commit();
                $count++;
                $this->command->info("[$count/{$allLayanan->count()}] Permohonan {$permohonan->nomor_registrasi} untuk layanan \"{$layanan->nama}\" berhasil dibuat");
            } catch (\Exception $e) {
                DB::rollBack();
                $this->command->error("Gagal membuat permohonan untuk layanan \"{$layanan->nama}\": " . $e->getMessage());
            }
        }

        $this->command->info("\n✓ Selesai! Total $count permohonan test berhasil dibuat.");
    }
}
