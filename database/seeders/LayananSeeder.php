<?php

namespace Database\Seeders;

use App\Models\Layanan;
use Illuminate\Database\Seeder;

class LayananSeeder extends Seeder
{
    public function run(): void
    {
        $layananData = [
            [
                'nama' => 'Surat Keterangan Domisili',
                'deskripsi' => 'Surat keterangan tempat tinggal untuk berbagai keperluan administrasi',
                'kategori' => 'surat',
                'persyaratan' => json_encode(['KTP asli', 'KK asli', 'Surat pengantar RT/RW']),
                'waktu_proses' => '1-2 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Surat Keterangan Tidak Mampu (SKTM)',
                'deskripsi' => 'Surat keterangan untuk bantuan sosial, beasiswa, dan keringanan biaya',
                'kategori' => 'surat',
                'persyaratan' => json_encode(['KTP asli', 'KK asli', 'Surat pengantar RT/RW', 'Foto rumah tampak depan']),
                'waktu_proses' => '2-3 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Pengantar KTP',
                'deskripsi' => 'Surat pengantar untuk pembuatan KTP baru, perpanjangan, atau penggantian',
                'kategori' => 'kependudukan',
                'persyaratan' => json_encode(['KK asli', 'Akta kelahiran', 'Pas foto 4x6 (2 lembar)', 'Surat pengantar RT/RW']),
                'waktu_proses' => '1 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Surat Keterangan Usaha',
                'deskripsi' => 'Surat keterangan untuk keperluan usaha, izin usaha, dan pengajuan kredit',
                'kategori' => 'surat',
                'persyaratan' => json_encode(['KTP asli', 'KK asli', 'Surat pengantar RT/RW', 'Foto tempat usaha', 'NPWP (jika ada)']),
                'waktu_proses' => '2-3 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Pengantar Kartu Keluarga (KK)',
                'deskripsi' => 'Surat pengantar untuk pembuatan KK baru, perubahan data, atau penambahan anggota keluarga',
                'kategori' => 'kependudukan',
                'persyaratan' => json_encode(['KTP asli semua anggota keluarga', 'KK lama (jika ada)', 'Akta nikah/cerai', 'Surat pengantar RT/RW', 'Akta kelahiran anak']),
                'waktu_proses' => '1-2 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Surat Keterangan Kelahiran',
                'deskripsi' => 'Surat pengantar untuk pembuatan akta kelahiran di Dinas Kependudukan',
                'kategori' => 'kependudukan',
                'persyaratan' => json_encode(['KTP kedua orang tua', 'KK asli', 'Buku nikah orang tua', 'Surat keterangan lahir dari bidan/rumah sakit', 'Surat pengantar RT/RW']),
                'waktu_proses' => '1 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Surat Keterangan Kematian',
                'deskripsi' => 'Surat pengantar untuk pembuatan akta kematian',
                'kategori' => 'kependudukan',
                'persyaratan' => json_encode(['KTP almarhum/almarhumah', 'KK asli', 'Surat keterangan kematian dari rumah sakit/dokter', 'KTP pelapor', 'Surat pengantar RT/RW']),
                'waktu_proses' => '1 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Surat Keterangan Pindah',
                'deskripsi' => 'Surat keterangan untuk pindah domisili ke kelurahan/kecamatan lain',
                'kategori' => 'kependudukan',
                'persyaratan' => json_encode(['KTP asli', 'KK asli', 'Surat pengantar RT/RW', 'Surat pernyataan pindah', 'Surat keterangan pindah dari kelurahan asal']),
                'waktu_proses' => '2-3 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Surat Keterangan Penghasilan',
                'deskripsi' => 'Surat keterangan untuk keperluan kredit bank atau administrasi lainnya',
                'kategori' => 'surat',
                'persyaratan' => json_encode(['KTP asli', 'KK asli', 'Surat pengantar RT/RW', 'Slip gaji/surat keterangan usaha', 'NPWP']),
                'waktu_proses' => '2-3 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Surat Pengantar Nikah (N1-N4)',
                'deskripsi' => 'Surat pengantar untuk keperluan menikah di KUA',
                'kategori' => 'surat',
                'persyaratan' => json_encode(['KTP asli calon pengantin', 'KK asli', 'Akta kelahiran', 'Pas foto 4x6 (4 lembar)', 'Surat keterangan belum menikah dari RT/RW', 'Ijazah terakhir']),
                'waktu_proses' => '1-2 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Surat Keterangan Kehilangan',
                'deskripsi' => 'Surat keterangan untuk melaporkan kehilangan dokumen atau barang',
                'kategori' => 'surat',
                'persyaratan' => json_encode(['KTP asli', 'KK asli', 'Surat pengantar RT/RW', 'Surat keterangan kehilangan dari kepolisian']),
                'waktu_proses' => '1 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Surat Keterangan Belum Menikah',
                'deskripsi' => 'Surat keterangan status belum menikah untuk keperluan administrasi',
                'kategori' => 'surat',
                'persyaratan' => json_encode(['KTP asli', 'KK asli', 'Akta kelahiran', 'Surat pengantar RT/RW', 'Pas foto 4x6 (2 lembar)']),
                'waktu_proses' => '1-2 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Surat Keterangan Catatan Kepolisian (SKCK)',
                'deskripsi' => 'Surat pengantar untuk pembuatan SKCK di kepolisian',
                'kategori' => 'surat',
                'persyaratan' => json_encode(['KTP asli', 'KK asli', 'Akta kelahiran', 'Pas foto 4x6 (6 lembar)', 'Surat pengantar RT/RW']),
                'waktu_proses' => '1 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Surat Keterangan Janda/Duda',
                'deskripsi' => 'Surat keterangan status cerai mati atau cerai hidup',
                'kategori' => 'surat',
                'persyaratan' => json_encode(['KTP asli', 'KK asli', 'Akta kematian/Akta cerai', 'Surat pengantar RT/RW']),
                'waktu_proses' => '1-2 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Surat Keterangan Beda Identitas',
                'deskripsi' => 'Surat keterangan untuk perbedaan data pada dokumen',
                'kategori' => 'surat',
                'persyaratan' => json_encode(['KTP asli', 'KK asli', 'Dokumen yang berbeda datanya', 'Surat pengantar RT/RW']),
                'waktu_proses' => '2-3 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Surat Keterangan Pernah Menikah',
                'deskripsi' => 'Surat keterangan status pernah menikah untuk keperluan administrasi',
                'kategori' => 'surat',
                'persyaratan' => json_encode(['KTP asli', 'KK asli', 'Buku nikah', 'Surat pengantar RT/RW']),
                'waktu_proses' => '1-2 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Surat Keterangan Waris',
                'deskripsi' => 'Surat keterangan untuk keperluan warisan',
                'kategori' => 'surat',
                'persyaratan' => json_encode(['KTP asli ahli waris', 'KK asli', 'Akta kematian pewaris', 'Surat keterangan ahli waris dari RT/RW', 'KTP saksi (2 orang)']),
                'waktu_proses' => '3-5 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Surat Keterangan Kepemilikan Tanah',
                'deskripsi' => 'Surat keterangan untuk kepemilikan tanah/bangunan',
                'kategori' => 'surat',
                'persyaratan' => json_encode(['KTP asli', 'KK asli', 'Bukti kepemilikan (sertifikat/girik)', 'Surat pengantar RT/RW', 'PBB terakhir']),
                'waktu_proses' => '3-5 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Surat Izin Keramaian',
                'deskripsi' => 'Surat izin untuk mengadakan acara/kegiatan yang mengumpulkan massa',
                'kategori' => 'surat',
                'persyaratan' => json_encode(['KTP penanggungjawab', 'KK asli', 'Proposal kegiatan', 'Surat pengantar RT/RW', 'Denah lokasi']),
                'waktu_proses' => '3-5 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Surat Keterangan Penelitian',
                'deskripsi' => 'Surat keterangan untuk keperluan penelitian atau riset',
                'kategori' => 'surat',
                'persyaratan' => json_encode(['KTP asli', 'Surat pengantar dari kampus/instansi', 'Proposal penelitian', 'KTM (untuk mahasiswa)']),
                'waktu_proses' => '2-3 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            // Kategori Keamanan
            [
                'nama' => 'Surat Pengantar SKCK',
                'deskripsi' => 'Surat pengantar untuk pembuatan Surat Keterangan Catatan Kepolisian',
                'kategori' => 'keamanan',
                'persyaratan' => json_encode(['KTP asli', 'KK asli', 'Akta kelahiran', 'Pas foto 4x6 (6 lembar)', 'Surat pengantar RT/RW']),
                'waktu_proses' => '1 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Surat Keterangan Kehilangan',
                'deskripsi' => 'Surat keterangan untuk melaporkan kehilangan dokumen/barang berharga',
                'kategori' => 'keamanan',
                'persyaratan' => json_encode(['KTP asli', 'KK asli', 'Surat pengantar RT/RW', 'Kronologi kejadian']),
                'waktu_proses' => '1 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Surat Keterangan Kepemilikan Kendaraan',
                'deskripsi' => 'Surat keterangan kepemilikan kendaraan bermotor',
                'kategori' => 'keamanan',
                'persyaratan' => json_encode(['KTP asli', 'KK asli', 'BPKB asli', 'STNK asli', 'Surat pengantar RT/RW', 'Kwitansi pembelian']),
                'waktu_proses' => '2-3 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Surat Izin Keramaian/Hajatan',
                'deskripsi' => 'Surat izin untuk mengadakan acara hajatan atau keramaian',
                'kategori' => 'keamanan',
                'persyaratan' => json_encode(['KTP penanggungjawab', 'KK asli', 'Surat pengantar RT/RW', 'Rencana acara', 'Denah lokasi']),
                'waktu_proses' => '3-5 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Surat Keterangan Jalan',
                'deskripsi' => 'Surat keterangan untuk keperluan perjalanan',
                'kategori' => 'keamanan',
                'persyaratan' => json_encode(['KTP asli', 'KK asli', 'Surat pengantar RT/RW', 'Tujuan perjalanan']),
                'waktu_proses' => '1 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            // Kategori Perizinan
            [
                'nama' => 'Izin Mendirikan Bangunan (IMB)',
                'deskripsi' => 'Surat pengantar untuk pembuatan IMB di instansi terkait',
                'kategori' => 'perizinan',
                'persyaratan' => json_encode(['KTP asli', 'KK asli', 'Sertifikat tanah', 'Gambar denah bangunan', 'PBB terakhir', 'Surat pengantar RT/RW']),
                'waktu_proses' => '3-5 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Izin Usaha Mikro Kecil (IUMK)',
                'deskripsi' => 'Surat pengantar untuk pembuatan izin usaha mikro dan kecil',
                'kategori' => 'perizinan',
                'persyaratan' => json_encode(['KTP asli', 'KK asli', 'NPWP', 'Pas foto 4x6', 'Surat pengantar RT/RW', 'Foto tempat usaha']),
                'waktu_proses' => '5-7 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Izin Tempat Usaha',
                'deskripsi' => 'Surat izin untuk membuka tempat usaha',
                'kategori' => 'perizinan',
                'persyaratan' => json_encode(['KTP asli', 'KK asli', 'Bukti kepemilikan/sewa tempat', 'Denah lokasi', 'Surat pengantar RT/RW', 'NPWP']),
                'waktu_proses' => '5-7 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Izin Gangguan (HO)',
                'deskripsi' => 'Surat pengantar untuk pembuatan izin gangguan/HO',
                'kategori' => 'perizinan',
                'persyaratan' => json_encode(['KTP asli', 'KK asli', 'IMB', 'Bukti kepemilikan tempat', 'Denah lokasi', 'Surat pengantar RT/RW', 'NPWP']),
                'waktu_proses' => '5-7 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Izin Usaha Perdagangan',
                'deskripsi' => 'Surat pengantar untuk izin usaha perdagangan',
                'kategori' => 'perizinan',
                'persyaratan' => json_encode(['KTP asli', 'KK asli', 'NPWP', 'Akta pendirian (untuk PT/CV)', 'Bukti kepemilikan tempat', 'Surat pengantar RT/RW']),
                'waktu_proses' => '7-10 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Izin Reklame',
                'deskripsi' => 'Surat pengantar untuk pembuatan izin pemasangan reklame',
                'kategori' => 'perizinan',
                'persyaratan' => json_encode(['KTP asli', 'KK asli', 'Desain reklame', 'Ukuran reklame', 'Foto lokasi', 'Surat pengantar RT/RW']),
                'waktu_proses' => '3-5 hari kerja',
                'biaya' => 'Gratis',
                'status' => 'aktif'
            ],
        ];

        foreach ($layananData as $layanan) {
            Layanan::create($layanan);
        }
    }
}
