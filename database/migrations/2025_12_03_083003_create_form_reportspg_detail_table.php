<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('form_reportspg_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id');
            $table->unsignedBigInteger('item_master_id')->nullable();
            $table->string('nama_barang');
            $table->string('sku');
            $table->string('ukuran')->nullable();
            $table->integer('qty_terjual')->default(0);
            $table->integer('qty_masuk')->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('report_id')->references('id')->on('form_reportspg')->onDelete('cascade');
            $table->foreign('item_master_id')->references('id')->on('item_master')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('form_reportspg_detail');
    }
};