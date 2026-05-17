<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    protected $fillable = [
        'nomor_antrian',
        'nama',
        'poli',
        'status',
        'jam_daftar',
    ];

    protected $casts = [
        'jam_daftar' => 'datetime',
    ];

    // Generate nomor antrian berikutnya untuk hari ini (format: 001, 002, ...)
    public static function nomorBerikutnya(): string
    {
        $last = self::whereDate('created_at', today())->max('nomor_antrian');
        $next = $last ? ((int) $last) + 1 : 1;
        return str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    // Query antrian hari ini
    public static function hariIni()
    {
        return self::whereDate('created_at', today());
    }
}