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
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id('idpesanan');
            $table->unsignedBigInteger('idvendor');
            $table->string('nama');
            $table->integer('total');
            $table->enum('metode_bayar', ['virtual_account', 'qris']);
            $table->enum('status_bayar', ['pending', 'lunas']);
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('midtrans_order_id')->nullable();
            $table->text('catatan')->nullable();
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
        Schema::dropIfExists('pesanan');
    }
};
