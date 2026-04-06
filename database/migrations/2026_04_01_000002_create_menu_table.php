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
        Schema::create('menu', function (Blueprint $table) {
            $table->id('idmenu');
            $table->unsignedBigInteger('idvendor');
            $table->string('nama_menu');
            $table->integer('harga');
            $table->string('path_gambar')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            
            $table->foreign('idvendor')
                ->references('idvendor')
                ->on('vendor')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
