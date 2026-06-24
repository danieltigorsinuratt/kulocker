<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pemesanan_id');
            $table->decimal('jumlah', 10, 2);
            $table->string('metode', 50);
            $table->enum('status', ['pending', 'lunas', 'gagal'])->default('pending');
            $table->string('bukti', 255)->nullable()->comment('Path file bukti transfer');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('pemesanan_id')->references('id')->on('pemesanan')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
