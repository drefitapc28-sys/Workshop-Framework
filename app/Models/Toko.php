<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    protected $table = 'toko';
    protected $fillable = [
        'nama_toko',
        'alamat',
        'latitude',
        'longitude',
        'accuracy',
        'barcode',
    ];

    public function kunjungan()
    {
        return $this->hasMany(Kunjungan::class, 'toko_id');
    }
}
