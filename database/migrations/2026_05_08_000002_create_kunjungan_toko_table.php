<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kunjungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toko_id')->constrained('toko')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Titik posisi sales saat kunjungan
            $table->decimal('latitude_sales', 10, 7);
            $table->decimal('longitude_sales', 10, 7);
            $table->decimal('accuracy_sales', 8, 2);
            // Hasil perhitungan
            $table->decimal('jarak_meter', 10, 2); // jarak actual (Haversine)
            $table->decimal('threshold_efektif', 10, 2); // threshold + acc toko + acc sales
            $table->enum('status', ['diterima', 'ditolak']);
            $table->integer('radius_threshold')->default(300); // meter, ditentukan sistem
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungan');
    }
};
