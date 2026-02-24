<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;

class CheckKepegawaianNotification extends Command
{
    protected $signature = 'kepegawaian:check-notification';
    protected $description = 'Cek notifikasi kenaikan pangkat dan gaji berkala';

    public function handle()
    {
        $pangkatTerlambat = Employee::kenaikanPangkatTerlambat()->count();
        $pangkatSegera    = Employee::kenaikanPangkatSegera(90)->count();
        $gajiTerlambat    = Employee::kenaikanGajiTerlambat()->count();
        $gajiSegera       = Employee::kenaikanGajiSegera(90)->count();

        $this->info("=== NOTIFIKASI KEPEGAWAIAN ===");
        $this->line("Kenaikan Pangkat terlambat : {$pangkatTerlambat} karyawan");
        $this->line("Kenaikan Pangkat segera    : {$pangkatSegera} karyawan");
        $this->line("Kenaikan Gaji terlambat    : {$gajiTerlambat} karyawan");
        $this->line("Kenaikan Gaji segera       : {$gajiSegera} karyawan");

        Employee::kenaikanPangkatTerlambat()->each(function($emp) {
            $this->error("  ✗ PANGKAT: {$emp->nama_lengkap} | Gol {$emp->golongan_ruang} | TMT: {$emp->tmt_golongan->format('d/m/Y')} | Terlambat " . abs($emp->kenaikan_pangkat_days_left) . " hari");
        });

        Employee::kenaikanPangkatSegera(90)->each(function($emp) {
            $this->warn("  ⚠ PANGKAT: {$emp->nama_lengkap} | Due: {$emp->kenaikan_pangkat_due_date->format('d/m/Y')} | {$emp->kenaikan_pangkat_days_left} hari lagi");
        });

        Employee::kenaikanGajiTerlambat()->each(function($emp) {
            $this->error("  ✗ GAJI: {$emp->nama_lengkap} | Gol {$emp->golongan_ruang} | TMT: {$emp->tmt_golongan->format('d/m/Y')} | Terlambat " . abs($emp->kenaikan_gaji_days_left) . " hari");
        });

        Employee::kenaikanGajiSegera(90)->each(function($emp) {
            $this->warn("  ⚠ GAJI: {$emp->nama_lengkap} | Due: {$emp->kenaikan_gaji_due_date->format('d/m/Y')} | {$emp->kenaikan_gaji_days_left} hari lagi");
        });

        $this->info('Pengecekan selesai.');
    }
}