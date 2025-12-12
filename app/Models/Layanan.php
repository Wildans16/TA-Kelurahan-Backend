<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Layanan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'layanan';

    protected $fillable = [
        'nama',
        'deskripsi',
        'kategori',
        'persyaratan',
        'waktu_proses',
        'biaya',
        'status',
    ];

    protected $casts = [
        'persyaratan' => 'array',
    ];

    public function permohonan()
    {
        return $this->hasMany(Permohonan::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
