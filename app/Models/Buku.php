<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'buku';
    protected $primaryKey = 'idbuku';
    public $timestamps = false;

    protected $fillable = [
        'kode',
        'judul',
        'pengarang',
        'idkategori',
    ];

    // Relasi many-to-one dengan Kategori
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'idkategori', 'idkategori');
    }
}
