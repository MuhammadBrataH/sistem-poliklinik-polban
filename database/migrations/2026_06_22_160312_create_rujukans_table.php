<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rujukans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rekam_medis_id')->unique()->constrained('rekam_medis')->cascadeOnDelete()->comment('UNIQUE Constraint 1-to-1');
            $table->string('rs_tujuan');
            $table->string('poli_tujuan');
            $table->text('catatan')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rujukans');
    }
};
