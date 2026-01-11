<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #10B981; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
        .info-box { background: white; padding: 20px; margin: 20px 0; border-left: 4px solid #10B981; border-radius: 4px; }
        .info-row { margin: 10px 0; }
        .label { font-weight: bold; color: #6B7280; }
        .value { color: #111827; }
        .success-box { background: #D1FAE5; padding: 15px; border-radius: 4px; margin: 20px 0; text-align: center; border: 2px solid #10B981; }
        .footer { text-align: center; color: #6B7280; font-size: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Permohonan Anda Telah Selesai</h1>
            <p>Kelurahan Graha Indah</p>
        </div>
        
        <div class="content">
            <p>Yth. Bapak/Ibu <strong>{{ $permohonan->nama }}</strong>,</p>
            
            <div class="success-box">
                <h2 style="margin: 0; color: #10B981;">🎉 Dokumen Anda Sudah Siap!</h2>
            </div>
            
            <p>Permohonan Anda telah selesai diproses dan dokumen sudah dapat diambil.</p>
            
            <div class="info-box">
                <h3 style="margin-top: 0; color: #10B981;">📋 Detail Permohonan</h3>
                <div class="info-row">
                    <span class="label">No. Registrasi:</span>
                    <span class="value"><strong>{{ $permohonan->nomor_registrasi }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="label">Layanan:</span>
                    <span class="value">{{ $permohonan->layanan->nama }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Tanggal Selesai:</span>
                    <span class="value">{{ now()->format('d F Y, H:i') }} WIB</span>
                </div>
                @if(!empty($permohonan->catatan))
                <div class="info-row">
                    <span class="label">Catatan:</span>
                    <div class="value" style="margin-top: 5px; padding: 10px; background: #FEF3C7; border-radius: 4px;">
                        {{ $permohonan->catatan }}
                    </div>
                </div>
                @endif
            </div>
            
            <div style="background: #EEF2FF; padding: 20px; border-radius: 4px; margin: 20px 0;">
                <h4 style="margin-top: 0; color: #4F46E5;">📍 Lokasi Pengambilan Dokumen:</h4>
                <p style="margin: 5px 0;"><strong>Kantor Kelurahan Graha Indah</strong></p>
                <p style="margin: 5px 0; color: #6B7280; font-size: 14px;">
                    Silakan membawa KTP/identitas asli dan nomor registrasi ini untuk pengambilan dokumen.
                </p>
            </div>
            
            <p style="color: #6B7280; font-size: 14px; text-align: center;">
                Terima kasih telah menggunakan layanan Kelurahan Graha Indah
            </p>
        </div>
        
        <div class="footer">
            <p><strong>Kelurahan Graha Indah</strong></p>
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
            <p>Untuk informasi lebih lanjut, silakan hubungi kantor kelurahan kami.</p>
        </div>
    </div>
</body>
</html>
