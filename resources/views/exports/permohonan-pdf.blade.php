<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Permohonan</title>
    <style>
        @page {
            margin: 100px 50px;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
        }
        
        header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        
        header h2 {
            margin: 5px 0 0 0;
            font-size: 14px;
            font-weight: normal;
            color: #666;
        }
        
        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 5px;
            font-size: 8px;
            color: #666;
        }
        
        .info-box {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f3f4f6;
            border-radius: 4px;
        }
        
        .info-box p {
            margin: 3px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 6px 4px;
            text-align: left;
        }
        
        th {
            background-color: #3b82f6;
            color: white;
            font-weight: bold;
            font-size: 9px;
        }
        
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .status {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .status-baru {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-proses {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .status-selesai {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-ditolak {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <header>
        <h1>KELURAHAN GRAHA INDAH</h1>
        <h2>Laporan Data Permohonan</h2>
    </header>

    <footer>
        <p>Dicetak pada: {{ date('d F Y H:i') }} | Halaman <span class="pageNumber"></span> dari <span class="totalPages"></span></p>
        <p>Kelurahan Graha Indah - Kota Balikpapan</p>
    </footer>

    <main>
        <div class="info-box">
            <p><strong>Periode Laporan:</strong> 
                @if($start_date && $end_date)
                    {{ date('d F Y', strtotime($start_date)) }} - {{ date('d F Y', strtotime($end_date)) }}
                @else
                    Semua Data
                @endif
            </p>
            <p><strong>Status:</strong> 
                @if(empty($status) || $status === 'all')
                    Semua Status
                @else
                    {{ ucfirst($status) }}
                @endif
            </p>
            <p><strong>Total Permohonan:</strong> {{ count($data) }} permohonan</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="10%">No. Registrasi</th>
                    <th width="12%">Nama</th>
                    <th width="11%">NIK</th>
                    <th width="15%">Layanan</th>
                    <th width="8%">Status</th>
                    <th width="9%">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $item->nomor_registrasi ?? '-' }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->nik }}</td>
                    <td>{{ $item->layanan->nama ?? '-' }}</td>
                    <td>
                        <span class="status status-{{ $item->status }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    <td>{{ date('d/m/Y', strtotime($item->created_at)) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        @if(count($data) === 0)
        <div style="text-align: center; padding: 40px; color: #666;">
            <p>Tidak ada data permohonan untuk ditampilkan</p>
        </div>
        @endif
    </main>
</body>
</html>
