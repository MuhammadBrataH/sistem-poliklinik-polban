<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pasiens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete()->comment('Kosong jika pasien Go-Show/Umum belum bikin akun web');
            $table->string('no_rm')->unique()->comment('Auto-generate: RM-2026-0001');
            $table->string('nik')->unique();
            $table->string('nama_lengkap');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->char('jenis_kelamin', 1)->comment('L atau P');
            $table->enum('kategori', ['mahasiswa', 'pegawai', 'keluarga_pegawai', 'umum']);
            $table->string('nim_nip')->nullable();
            $table->string('prodi_unit')->nullable()->comment('Jurusan mhs atau Unit kerja pegawai');
            $table->string('no_telp');
            $table->text('alamat');
            $table->string('status_bpjs')->nullable();
            $table->string('nama_penanggung')->nullable()->comment('Untuk mahasiswa atau keluarga pegawai');
            $table->string('hubungan_penanggung')->nullable()->comment('Contoh: Anak, Istri, Suami');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pasiens');
    }
};
