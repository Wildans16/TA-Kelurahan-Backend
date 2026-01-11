<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;
use App\Mail\BalasanPesanKontak;

echo "Testing email with Resend...\n\n";

$kontak = new stdClass();
$kontak->nama = 'Test User';
$kontak->email = 'hogewar@gmail.com';
$kontak->pesan = 'Test pesan kontak dari sistem';

$balasan = 'Terima kasih atas pesan Anda. Ini adalah balasan test dari sistem Kelurahan Graha Indah. Jika Anda menerima email ini, berarti konfigurasi email sudah bekerja dengan baik!';

try {
    echo "Sending email to: {$kontak->email}\n";
    echo "Via: Resend API\n\n";
    
    Mail::mailer('resend')->send(new BalasanPesanKontak($kontak, $balasan));
    
    echo "✅ SUCCESS! Email berhasil dikirim via Resend API\n";
    echo "Cek inbox di hogewar@gmail.com\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
