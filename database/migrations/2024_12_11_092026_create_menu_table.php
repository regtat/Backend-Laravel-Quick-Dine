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
            $table->id();
            $table->string('nama_menu');
            $table->string('deskripsi');
            $table->integer('harga');
            $table->string('image')->nullable();
            $table->integer('stok');
            $table->unsignedBigInteger('id_kantin');
            $table->timestamps();

            $table->foreign('id_kantin')->references('id')->on('kantin')->onDelete('cascade');
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
