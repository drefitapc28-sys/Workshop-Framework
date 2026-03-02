<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void //fungsi up digunakan untuk menambahkan kolom google_id dan otp ke tabel users, dengan tipe data string dan nullable (boleh kosong).
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id', 255)->nullable(); 
            $table->string('otp', 6)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void //fungsi down digunakan untuk menghapus kolom google_id dan otp dari tabel users jika kita melakukan rollback pada migration ini.
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('google_id', 'otp');
        });
    }
};
