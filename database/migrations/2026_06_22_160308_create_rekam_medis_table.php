<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('antrean_id')->constrained('antreans')->cascadeOnDelete();
            $table->foreignId('pasien_id')->constrained('pasiens')->cascadeOnDelete();
            $table->foreignId('dokter_id')->constrained('dokters')->cascadeOnDelete();
            
            // Screening
            $table->boolean('alergi_obat')->default(false);
            $table->boolean('gangguan_ginjal')->default(false);
            $table->boolean('menyusui')->default(false);
            
            // SOAP
            $table->text('keluhan_subjektif');
            $table->text('pemeriksaan_objektif');
            $table->text('diagnosa');
            $table->text('plan_penatalaksanaan')->comment('Instruksi dokter/nasihat medis');
            $table->boolean('butuh_rujukan')->default(false);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekam_medis');
    }
};
