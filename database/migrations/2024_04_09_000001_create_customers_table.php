<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            
            // Data customer
            $table->string('nama');
            $table->text('alamat');
            $table->string('provinsi');
            $table->string('kota');
            $table->string('kecamatan');
            $table->string('kodepos_keluarahan');
            
            // Foto
            // Untuk BLOB storage (Studi Kasus 1) - menggunakan binary untuk menyimpan image data
            $table->binary('foto')->nullable();
            
            // Untuk File storage (Studi Kasus 2) - file path di storage publik
            $table->string('foto_path')->nullable();
            
            // Tracking tipe storage
            $table->enum('tipe_foto', ['blob', 'file'])->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
