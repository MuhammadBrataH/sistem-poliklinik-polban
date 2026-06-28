<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('antreans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasiens')->cascadeOnDelete();
            $table->foreignId('jadwal_praktik_id')->constrained('jadwal_praktiks')->cascadeOnDelete();
            $table->string('nomor_antrean')->comment('Contoh: U-015');
            $table->enum('status', ['menunggu_konfirmasi', 'terkonfirmasi', 'hadir', 'diperiksa', 'pending', 'menunggu_obat', 'selesai', 'batal'])->default('menunggu_konfirmasi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('antreans');
    }
};
