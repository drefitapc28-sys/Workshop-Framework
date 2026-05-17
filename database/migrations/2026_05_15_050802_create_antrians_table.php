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
        Schema::create('antrians', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_antrian', 3);
            $table->string('nama', 100);
            $table->string('poli', 100);
            $table->enum('status', ['menunggu', 'dipanggil', 'terlambat', 'selesai'])->default('menunggu');
            $table->timestamp('jam_daftar')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antrians');
    }
};
