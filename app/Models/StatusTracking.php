<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusTracking extends Model
{
    use HasFactory;

    protected $table = 'status_tracking';

    protected $fillable = [
        'permohonan_id',
        'step',
        'tanggal',
        'completed',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'completed' => 'boolean',
    ];

    public function permohonan()
    {
        return $this->belongsTo(Permohonan::class);
    }
}
