<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kategori;
use App\Models\Buku;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
       
        User::factory()->create([
            'name' => 'Drefita',
            'email' => 'drefita@mail.com',
            'password' => Hash::make('123456'),
        ]);

        Kategori::insert([
            ['idkategori' => 1, 'nama_kategori' => 'Novel'],
            ['idkategori' => 2, 'nama_kategori' => 'Puisi'],
            ['idkategori' => 3, 'nama_kategori' => 'Drama'],
        ]);

        // Reset sequence untuk PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("SELECT setval(pg_get_serial_sequence('kategori', 'idkategori'), MAX(idkategori)) FROM kategori;");
        }

        Buku::insert([
            ['idbuku' => 1, 'kode' => 'BK-001', 'judul' => 'Home Sweet Loan', 'pengarang' => 'Almira Bastari', 'idkategori' => 1],
            ['idbuku' => 2, 'kode' => 'BK-002', 'judul' => 'Ayah', 'pengarang' => 'Guus Dur', 'idkategori' => 2],
        ]);

        // Reset sequence untuk PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("SELECT setval(pg_get_serial_sequence('buku', 'idbuku'), MAX(idbuku)) FROM buku;");
        }
    }
}
