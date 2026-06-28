<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obats', function (Blueprint $table) {
            $table->id();
            $table->string('nama_obat');
            $table->string('satuan')->comment('Contoh: Tablet, Botol, Strip');
            $table->integer('stok')->default(0);
            $table->string('gambar')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obats');
    }
};
