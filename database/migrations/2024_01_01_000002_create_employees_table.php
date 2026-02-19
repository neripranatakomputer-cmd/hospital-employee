<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // Data Pribadi
            $table->string('foto_profil')->nullable();
            $table->string('nip')->unique();
            $table->string('nik')->unique();
            $table->string('nama_lengkap');
            $table->string('nama_gelar')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);

            // Data Kontak
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();

            // Data Pendidikan
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('nomor_ijazah')->nullable();
            $table->year('tahun_lulus_ijazah')->nullable();
            $table->string('dokumen_ijazah')->nullable();

            // Data Kepegawaian
            $table->string('jabatan')->nullable();
            $table->string('unit')->nullable();
            $table->string('str_file')->nullable();
            $table->string('sip_file')->nullable();
            $table->date('tmt_sip')->nullable(); // Tanggal Mulai Tugas SIP
            $table->date('tat_sip')->nullable(); // Tanggal Akhir Tugas SIP

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
