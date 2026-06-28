<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_rujukans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_template')->comment('Misal: Rujukan Standar BPJS');
            $table->text('isi_template')->comment('Teks/HTML dengan placeholders');
            $table->boolean('is_active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_rujukans');
    }
};
