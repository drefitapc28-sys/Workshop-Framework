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
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');
            $table->foreignId('kartu_nfc_id')->constrained('kartu_nfcs')->onDelete('cascade');
            $table->string('serial_number');         
            $table->string('mata_kuliah')->nullable(); 
            $table->enum('status', ['hadir', 'terlambat'])->default('hadir');
            $table->timestamp('waktu_scan')->useCurrent();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
