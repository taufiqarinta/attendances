<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('report_stock_spg', function (Blueprint $table) {
            $table->id();
            $table->string('kode_report')->unique();
            $table->unsignedBigInteger('user_id');
            $table->string('nama_spg');
            $table->unsignedBigInteger('toko_id');
            $table->string('nama_toko');
            $table->integer('tahun');
            $table->integer('bulan');
            $table->integer('minggu_ke');
            $table->date('tanggal');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('toko_id')->references('id')->on('daftar_toko')->onDelete('cascade');
            $table->index(['tahun', 'bulan', 'minggu_ke', 'toko_id']);
            $table->unique(['tahun', 'bulan', 'minggu_ke', 'toko_id'], 'unique_stock_report');
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_stock_spg');
    }
};