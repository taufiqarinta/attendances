<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('report_stock_spg_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id');
            $table->string('item_code');
            $table->string('nama_barang');
            $table->string('ukuran')->nullable();
            $table->integer('stock');
            $table->integer('qty_masuk')->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('report_id')->references('id')->on('report_stock_spg')->onDelete('cascade');
            $table->index('item_code');
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_stock_spg_detail');
    }
};