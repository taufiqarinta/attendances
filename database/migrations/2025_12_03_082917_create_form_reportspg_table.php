<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('form_reportspg', function (Blueprint $table) {
            $table->id();
            $table->string('kode_report')->unique();
            $table->date('tanggal');
            $table->unsignedBigInteger('user_id');
            $table->string('nama_spg');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['tanggal', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('form_reportspg');
    }
};