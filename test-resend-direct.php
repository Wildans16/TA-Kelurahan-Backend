<?php

require __DIR__.'/vendor/autoload.php';

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use Resend\Resend;

echo "=== Testing Resend Email Direct ===\n\n";

try {
    $apiKey = $_ENV['RESEND_API_KEY'] ?? null;
    
    if (!$apiKey) {
        throw new Exception("RESEND_API_KEY tidak ditemukan di .env");
    }
    
    echo "📌 API Key: " . substr($apiKey, 0, 10) . "...\n";
    echo "📧 FROM: Kelurahan Graha Indah <onboarding@resend.dev>\n";
    echo "📧 TO: hogewar@gmail.com\n";
    echo "📧 SUBJECT: Balasan Pesan Anda - Kelurahan Graha Indah\n\n";
    
    echo "🚀 Sending email via Resend API...\n\n";
    
    // Create Resend client
    $resend = Resend::client($apiKey);
    
    // HTML content
    $htmlContent = '
    <div style="font-family: Arial, sans-serif; padding: 20px; max-width: 600px;">
        <h2 style="color: #2563eb;">KELURAHAN GRAHA INDAH</h2>
        <h3>Balasan Pesan Anda</h3>
        <p>Yth. Bapak/Ibu <strong>Test User</strong>,</p>
        <p>Terima kasih atas pesan Anda.</p>
        <div style="background: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <p><strong>Balasan:</strong></p>
            <p>Ini adalah balasan test dari sistem email notification Kelurahan Graha Indah.</p>
        </div>
        <hr style="margin: 20px 0; border: none; border-top: 1px solid #e5e7eb;">
        <p style="color: #6b7280; font-size: 14px;">
            Kelurahan Graha Indah<br>
            Email: hogewar@gmail.com
        </p>
    </div>
    ';
    
    // Send email
    $result = $resend->emails->send([
        'from' => 'Kelurahan Graha Indah <onboarding@resend.dev>',
        'to' => ['hogewar@gmail.com'],
        'subject' => 'Balasan Pesan Anda - Kelurahan Graha Indah',
        'html' => $htmlContent
    ]);
    
    echo "✅ SUCCESS! Email berhasil dikirim!\n\n";
    echo "📋 Response dari Resend:\n";
    print_r($result);
    echo "\n\n🎉 Silakan cek inbox/spam folder di hogewar@gmail.com\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n\n";
    echo "📋 Trace:\n" . $e->getTraceAsString() . "\n";
}
