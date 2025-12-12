<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permohonan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'permohonan';

    protected $fillable = [
        'nomor_registrasi',
        'layanan_id',
        'nama',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'rt',
        'rw',
        'no_hp',
        'email',
        'keperluan',
        'keterangan',
        'status',
        'estimasi_selesai',
        'catatan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'estimasi_selesai' => 'date',
    ];

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }

    public function berkas()
    {
        return $this->hasMany(Berkas::class);
    }

    public function statusTracking()
    {
        return $this->hasMany(StatusTracking::class);
    }

    public function generateNomorRegistrasi()
    {
        $year = date('Y');
        $month = date('m');
        $lastNumber = self::whereYear('created_at', $year)
                         ->whereMonth('created_at', $month)
                         ->count() + 1;
        
        return 'REQ' . $year . $month . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($permohonan) {
            $permohonan->nomor_registrasi = $permohonan->generateNomorRegistrasi();
        });
    }
}
