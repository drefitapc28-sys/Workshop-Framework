<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kategori;
use App\Models\Buku;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

//Seeder digunakan untuk mengisi data awal (dummy data) ke dalam database secara otomatis.

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
            ['nama_kategori' => 'Novel'],
            ['nama_kategori' => 'Puisi'],
            ['nama_kategori' => 'Drama'],
        ]);

        
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("SELECT setval(pg_get_serial_sequence('kategori', 'idkategori'), MAX(idkategori)) FROM kategori;");
        }

        Buku::insert([
            ['kode' => 'BK-001', 'judul' => 'Home Sweet Loan', 'pengarang' => 'Almira Bastari', 'idkategori' => 1],
        ]);

        // Reset sequence untuk PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("SELECT setval(pg_get_serial_sequence('buku', 'idbuku'), MAX(idbuku)) FROM buku;");
        }
    }
}
