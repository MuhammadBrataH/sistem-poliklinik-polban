<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tindakans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poli_id')->constrained('polis')->cascadeOnDelete()->comment('Membedakan tindakan Gigi dan Umum');
            $table->string('nama_tindakan');
            $table->integer('tarif')->default(0)->comment('Dinamis, bisa diubah oleh admin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tindakans');
    }
};
