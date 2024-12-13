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
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_kantin');
            $table->integer('total');
            $table->enum('status',['menunggu','diproses','selesai']);
            $table->enum('diantar_diambil', ['diantar', 'diambil']); //pilihan pengiriman
            $table->string('lok_pengantaran')->nullable();
            $table->string('metode_pembayaran');
            $table->string('bukti_pembayaran')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign( 'id_kantin')->references('id')->on('kantin')->onDelete('cascade');
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
