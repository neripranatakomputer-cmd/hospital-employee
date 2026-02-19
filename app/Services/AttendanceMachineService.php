<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use Rats\Zkteco\Lib\ZKTeco;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceMachineService
{
    private string $ip;
    private int $port;
    private ZKTeco $zk;

    public function __construct()
    {
        $this->ip   = config('services.attendance.ip', env('ATTENDANCE_MACHINE_IP', '192.168.1.100'));
        $this->port = (int) config('services.attendance.port', env('ATTENDANCE_MACHINE_PORT', 4370));
    }

    /**
     * Test koneksi ke mesin
     */
    public function testConnection(): array
    {
        try {
            $zk = new ZKTeco($this->ip, $this->port);
            $connected = $zk->connect();

            if ($connected) {
                $info = [
                    'connected'    => true,
                    'ip'           => $this->ip,
                    'port'         => $this->port,
                    'device_name'  => $zk->deviceName() ?? 'Unknown',
                    'serial'       => $zk->serialNumber() ?? 'Unknown',
                    'firmware'     => $zk->fmVersion() ?? 'Unknown',
                    'user_count'   => count($zk->getUser() ?? []),
                    'att_count'    => count($zk->getAttendance() ?? []),
                ];
                $zk->disconnect();
                return $info;
            }

            return ['connected' => false, 'error' => 'Tidak bisa terhubung ke mesin'];

        } catch (\Exception $e) {
            return ['connected' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Ambil semua log absensi dari mesin dan simpan ke database
     */
    public function syncAttendance(?string $date = null): array
    {
        $targetDate = $date ? Carbon::parse($date) : Carbon::today();
        $synced  = 0;
        $skipped = 0;
        $errors  = [];

        try {
            $zk = new ZKTeco($this->ip, $this->port);

            if (!$zk->connect()) {
                throw new \Exception("Gagal terhubung ke mesin di {$this->ip}:{$this->port}. Periksa IP dan koneksi jaringan.");
            }

            // Disable mesin sementara agar tidak konflik saat ambil data
            $zk->disableDevice();

            $logs = $zk->getAttendance();

            // Enable kembali setelah ambil data
            $zk->enableDevice();
            $zk->disconnect();

            if (empty($logs)) {
                return ['synced' => 0, 'skipped' => 0, 'errors' => [], 'message' => 'Tidak ada data di mesin'];
            }

            foreach ($logs as $log) {
                try {
                    // Parse data dari mesin
                    // Format log: ['uid' => '1', 'id' => 'NIP', 'state' => '1', 'timestamp' => '2024-01-01 08:00:00', 'type' => '0']
                    $nip       = (string) ($log['id'] ?? $log['uid'] ?? '');
                    $timestamp = Carbon::parse($log['timestamp']);
                    $logDate   = $timestamp->toDateString();

                    // Filter hanya tanggal yang diminta
                    if ($logDate !== $targetDate->toDateString()) {
                        continue;
                    }

                    // Cari karyawan berdasarkan NIP
                    $employee = Employee::where('nip', $nip)->first();
                    if (!$employee) {
                        $errors[] = "NIP {$nip} tidak ditemukan di database";
                        continue;
                    }

                    $time = $timestamp->format('H:i');

                    $existing = Attendance::where('employee_id', $employee->id)
                        ->where('tanggal', $logDate)
                        ->first();

                    if ($existing) {
                        // Jika sudah ada, update jam keluar jika waktu lebih akhir
                        if ($time > ($existing->jam_masuk ?? '00:00')) {
                            $existing->update([
                                'jam_keluar' => $time,
                                'sumber'     => 'mesin',
                            ]);
                        }
                        $skipped++;
                    } else {
                        // Buat record baru
                        Attendance::create([
                            'employee_id' => $employee->id,
                            'tanggal'     => $logDate,
                            'jam_masuk'   => $time,
                            'status'      => 'hadir',
                            'sumber'      => 'mesin',
                        ]);
                        $synced++;
                    }

                } catch (\Exception $e) {
                    $errors[] = "Log error: " . $e->getMessage();
                    Log::error('Attendance sync error', ['log' => $log, 'error' => $e->getMessage()]);
                }
            }

        } catch (\Exception $e) {
            Log::error('Machine connection error', ['error' => $e->getMessage()]);
            throw $e;
        }

        return [
            'synced'  => $synced,
            'skipped' => $skipped,
            'errors'  => $errors,
            'message' => "Berhasil sync {$synced} data, {$skipped} dilewati",
        ];
    }

    /**
     * Ambil daftar user yang terdaftar di mesin
     */
    public function getMachineUsers(): array
    {
        try {
            $zk = new ZKTeco($this->ip, $this->port);
            if (!$zk->connect()) {
                throw new \Exception("Gagal terhubung ke mesin");
            }
            $users = $zk->getUser() ?? [];
            $zk->disconnect();
            return $users;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Daftarkan karyawan ke mesin fingerprint
     */
    public function registerEmployee(Employee $employee): bool
    {
        try {
            $zk = new ZKTeco($this->ip, $this->port);
            if (!$zk->connect()) {
                throw new \Exception("Gagal terhubung ke mesin");
            }

            // uid = nomor urut di mesin, id = NIP karyawan, name = nama, privilege = 0 (user biasa)
            $uid = Employee::count(); // atau bisa pakai $employee->id
            $zk->setUser($uid, $employee->nip, $employee->nama_lengkap, '', 0);
            $zk->disconnect();
            return true;

        } catch (\Exception $e) {
            Log::error('Register employee to machine error', ['error' => $e->getMessage()]);
            return false;
        }
    }
}