<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akses_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('pemesanan_id');
            $table->unsignedInteger('user_id');
            $table->timestamp('waktu_akses')->useCurrent();
            $table->enum('jenis', ['buka', 'tutup']);
            $table->enum('status', ['berhasil', 'gagal']);
            $table->text('keterangan')->nullable();

            $table->foreign('pemesanan_id')->references('id')->on('pemesanan')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akses_log');
    }
};
