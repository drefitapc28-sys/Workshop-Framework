<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KartuNfc extends Model
{
    
    protected $table = 'kartu_nfcs';

    protected $fillable = [
        'mahasiswa_id',
        'serial_number',
        'label',
        'aktif',
        'didaftarkan_pada',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'didaftarkan_pada' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }
}