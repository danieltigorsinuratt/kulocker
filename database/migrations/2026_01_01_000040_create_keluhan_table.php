<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keluhan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('locker_id')->nullable();
            $table->unsignedInteger('pemesanan_id')->nullable();
            $table->string('judul', 150);
            $table->text('deskripsi');
            $table->enum('status', ['open', 'proses', 'selesai'])->default('open');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
            $table->foreign('locker_id')->references('id')->on('lockers')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('pemesanan_id')->references('id')->on('pemesanan')->onDelete('set null')->onUpdate('cascade');
        });

        Schema::create('keluhan_response', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('keluhan_id');
            $table->unsignedInteger('admin_id');
            $table->text('pesan');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('keluhan_id')->references('id')->on('keluhan')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('admin_id')->references('id')->on('users')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keluhan_response');
        Schema::dropIfExists('keluhan');
    }
};
