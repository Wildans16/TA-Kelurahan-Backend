<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PermohonanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Registrasi',
            'Nama',
            'NIK',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Alamat',
            'RT/RW',
            'No. HP',
            'Email',
            'Layanan',
            'Keperluan',
            'Status',
            'Tanggal Pengajuan',
        ];
    }

    public function map($permohonan): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $permohonan->nomor_registrasi ?? '-',
            $permohonan->nama,
            "'" . $permohonan->nik, // Prefix with ' to prevent Excel treating as number
            $permohonan->tempat_lahir,
            date('d-m-Y', strtotime($permohonan->tanggal_lahir)),
            $permohonan->jenis_kelamin,
            $permohonan->alamat,
            $permohonan->rt . '/' . $permohonan->rw,
            "'" . $permohonan->no_hp,
            $permohonan->email,
            $permohonan->layanan->nama ?? '-',
            $permohonan->keperluan,
            ucfirst($permohonan->status),
            date('d-m-Y H:i', strtotime($permohonan->created_at)),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style header row
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '3B82F6']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}
