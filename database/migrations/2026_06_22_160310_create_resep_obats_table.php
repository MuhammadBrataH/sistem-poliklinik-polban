<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resep_obats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rekam_medis_id')->constrained('rekam_medis')->cascadeOnDelete();
            $table->foreignId('obat_id')->constrained('obats')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->string('aturan_pakai')->comment('Contoh: 3x Sehari Sesudah Makan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resep_obats');
    }
};
