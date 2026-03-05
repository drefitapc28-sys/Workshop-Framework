<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang'; // menentukan nama tabel yaitu 'barang'
    protected $primaryKey = 'id_barang';
    public $incrementing = false; // karena id dibuat oleh triggger, bukan auto increment
    protected $keyType = 'string';
    public $timestamps = false; // karena tidak menggunakan kolom created_at dan updated_at

    protected $fillable = [
        'id_barang',
        'nama',
        'harga',
        'timestamp'
    ];

}
