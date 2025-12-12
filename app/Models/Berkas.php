<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Berkas extends Model
{
    protected $table = 'berkas';
    
    protected $fillable = [
        'permohonan_id',
        'jenis_berkas',
        'nama_file',
        'path',
        'mime_type',
        'size',
    ];

    protected $appends = ['url']; // Tambahkan ini

    public function permohonan()
    {
        return $this->belongsTo(Permohonan::class);
    }

    // Accessor untuk generate URL dari path
    public function getUrlAttribute()
    {
        return url('storage/' . $this->path);
    }
}
