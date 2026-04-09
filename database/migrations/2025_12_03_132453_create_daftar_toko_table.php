<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('daftar_toko', function (Blueprint $table) {
            $table->id();
            $table->string('kode_spg');
            $table->string('nama_spg');
            $table->string('divisi')->nullable();
            $table->string('nama_toko');
            $table->string('kota')->nullable();
            $table->timestamps();
            
            $table->index('kode_spg');
            $table->index('nama_spg');
            $table->index('nama_toko');
        });
    }

    public function down()
    {
        Schema::dropIfExists('daftar_toko');
    }
};