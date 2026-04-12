<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'nama',
        'alamat',
        'provinsi',
        'kota',
        'kecamatan',
        'kodepos_keluarahan',
        'foto',        // BLOB data
        'foto_path',   // File path
        'tipe_foto',   // 'blob' or 'file'
    ];

    // Cast untuk handle data
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'foto' => 'string',
    ];
}
