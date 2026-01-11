<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4F46E5; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
        .info-box { background: white; padding: 20px; margin: 20px 0; border-left: 4px solid #4F46E5; border-radius: 4px; }
        .info-row { margin: 10px 0; }
        .label { font-weight: bold; color: #6B7280; }
        .value { color: #111827; }
        .highlight { background: #FEF3C7; padding: 15px; border-radius: 4px; margin: 20px 0; text-align: center; }
        .footer { text-align: center; color: #6B7280; font-size: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
        .button { display: inline-block; background: #4F46E5; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Permohonan Berhasil Diterima</h1>
            <p>Kelurahan Graha Indah</p>
        </div>
        
        <div class="content">
            <p>Yth. Bapak/Ibu <strong>{{ $permohonan->nama }}</strong>,</p>
            
            <p>Terima kasih telah mengajukan permohonan layanan di Kelurahan Graha Indah.</p>
            
            <div class="info-box">
                <h3 style="margin-top: 0; color: #4F46E5;">📋 Detail Permohonan</h3>
                <div class="info-row">
                    <span class="label">No. Registrasi:</span>
                    <span class="value"><strong>{{ $permohonan->nomor_registrasi }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="label">Layanan:</span>
                    <span class="value">{{ $permohonan->layanan->nama }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Tanggal Pengajuan:</span>
                    <span class="value">{{ $permohonan->created_at->format('d F Y, H:i') }} WIB</span>
                </div>
                @if($permohonan->estimasi_selesai)
                <div class="info-row">
                    <span class="label">Estimasi Selesai:</span>
                    <span class="value">{{ $permohonan->estimasi_selesai->format('d F Y') }}</span>
                </div>
                @endif
            </div>
            
            <div class="highlight">
                <strong>📌 Simpan nomor registrasi ini untuk pengecekan status permohonan Anda</strong>
            </div>
            
            <p style="text-align: center;">
                <a href="https://kelurahan-graha-indah-frontend.vercel.app/status" class="button">
                    Cek Status Permohonan
                </a>
            </p>
            
            <p style="color: #6B7280; font-size: 14px;">
                Anda juga dapat mengecek status permohonan dengan datang langsung ke Kantor Kelurahan Graha Indah dengan membawa nomor registrasi ini.
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
