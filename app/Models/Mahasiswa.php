<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $fillable = [
        'nim',
        'nama',
        'prodi',
        'kelas',
    ];

    public function kartuNfc()
    {
        return $this->hasMany(KartuNfc::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }
}