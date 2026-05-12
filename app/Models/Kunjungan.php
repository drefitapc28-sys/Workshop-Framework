<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
    protected $table = 'kunjungan';

    protected $fillable = [
        'toko_id',
        'user_id',
        'latitude_sales',
        'longitude_sales',
        'accuracy_sales',
        'jarak_meter',
        'threshold_efektif',
        'status',
        'radius_threshold',
    ];

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'toko_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
