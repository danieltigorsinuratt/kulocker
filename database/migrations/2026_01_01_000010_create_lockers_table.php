<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lockers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode_loker', 10)->unique()->comment('Contoh: A-01');
            $table->string('lokasi', 100)->comment('Contoh: Gedung A Lt.2');
            $table->enum('ukuran', ['S', 'M', 'L']);
            $table->enum('status', ['tersedia', 'terpakai', 'rusak'])->default('tersedia');
            $table->timestamp('created_at')->useCurrent();
            $table->tinyInteger('is_deleted')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lockers');
    }
};
