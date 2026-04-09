<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('kode_opname')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nama_spg');
            $table->foreignId('toko_id')->constrained('daftar_toko')->onDelete('cascade');
            $table->string('nama_toko');
            $table->integer('tahun');
            $table->integer('bulan');
            $table->date('tanggal');
            $table->enum('status', ['draft', 'disetujui', 'ditolak'])->default('draft');
            $table->timestamps();
            
            $table->index(['tahun', 'bulan']);
            $table->index('toko_id');
        });

        Schema::create('stock_opname_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opname_id')->constrained('stock_opnames')->onDelete('cascade');
            $table->string('item_code');
            $table->string('nama_barang');
            $table->string('ukuran')->nullable();
            $table->integer('stock')->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->foreign('item_code')->references('item_code')->on('item_master');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_opname_details');
        Schema::dropIfExists('stock_opnames');
    }
};