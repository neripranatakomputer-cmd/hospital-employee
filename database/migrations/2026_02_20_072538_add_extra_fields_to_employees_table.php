<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('agama')->nullable()->after('golongan_darah');
            $table->string('golongan_ruang')->nullable()->after('unit');
            $table->date('tmt_pns')->nullable()->after('golongan_ruang');
            $table->string('prodi_pendidikan')->nullable()->after('pendidikan_terakhir');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['agama', 'golongan_ruang', 'tmt_pns', 'prodi_pendidikan']);
        });
    }
};