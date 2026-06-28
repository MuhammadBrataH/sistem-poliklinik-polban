<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_praktiks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dokter_id')->constrained('dokters')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('kuota');
            $table->integer('sisa_kuota')->comment('Denormalisasi untuk performa query');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_praktiks');
    }
};
