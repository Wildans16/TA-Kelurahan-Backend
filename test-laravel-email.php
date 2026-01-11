<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;
use App\Mail\BalasanPesanKontak;

echo "=== Testing Laravel Email via Resend ===\n\n";

try {
    echo "1. Checking Resend config...\n";
    $resendKey = config('services.resend.key');
    $resendFrom = config('services.resend.from_email');
    
    if (!$resendKey) {
        die("❌ RESEND_API_KEY not found in config!\n");
    }
    
    echo "   ✅ API Key: " . substr($resendKey, 0, 15) . "...\n";
    echo "   ✅ FROM Email: " . $resendFrom . "\n\n";
    
    echo "2. Creating test email data...\n";
    $kontak = (object) [
        'id' => 999,
        'nama' => 'Test User',
        'email' => 'hogewar@gmail.com',
        'subjek' => 'Test Subject',
        'pesan' => 'Test message'
    ];
    echo "   ✅ Email TO: hogewar@gmail.com\n\n";
    
    echo "3. Sending email via Resend mailer...\n";
    Mail::mailer('resend')->send(
        new BalasanPesanKontak($kontak, 'Test reply from Laravel', 'onboarding@resend.dev', 'Kelurahan Graha Indah')
    );
    
    echo "   ✅ Email sent successfully!\n\n";
    echo "🎉 SUCCESS! Check inbox: hogewar@gmail.com\n";
    
} catch (\Exception $e) {
    echo "\n❌ ERROR OCCURRED:\n";
    echo "Message: " . $e->getMessage() . "\n\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

