<?php

namespace App\Models;

use illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensis';

    protected $fillable = [
        'mahasiswa_id',
        'kartu_nfc_id',
        'serial_number',
        'mata_kuliah',
        'status',
        'waktu_scan',
        'keterangan',
    ];

    protected $casts = [
        'waktu_scan' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function kartuNfc()
    {
        return $this->belongsTo(KartuNfc::class);
    }
}