<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';
    protected $primaryKey = 'idkategori';
    public $timestamps = false;

    protected $fillable = [
        'nama_kategori',
    ];

    // Relasi one-to-many dengan Buku   
    public function bukus()
    {
        return $this->hasMany(Buku::class, 'idkategori', 'idkategori');
    }
}
