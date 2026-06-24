<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->increments('id');
            $table->string('judul', 200);
            $table->text('isi');
            $table->enum('kategori', ['info', 'peringatan', 'promo', 'maintenance'])->default('info');
            $table->tinyInteger('is_aktif')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->dateTime('expired_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};
