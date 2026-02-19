<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;

/**
 * Service untuk sinkronisasi data dari mesin absensi (ZKTeco, dll)
 * Menggunakan library zklib atau API mesin absensi
 *
 * Install: composer require rats/zkteco
 */
class AttendanceMachineService
{
    private string $ip;
    private int $port;

    public function __construct()
    {
        $this->ip   = config('app.attendance_machine_ip', '192.168.1.100');
        $this->port = config('app.attendance_machine_port', 4370);
    }

    /**
     * Sync attendance data from machine to database
     */
    public function syncAttendance(?string $date = null): array
    {
        $date = $date ?? today()->format('Y-m-d');
        $synced = 0;
        $errors = [];

        try {
            // ZKTeco integration using rats/zkteco package
            // composer require rats/zkteco
            $zk = new \Rats\Zkteco\Lib\ZKTeco($this->ip, $this->port);

            if (!$zk->connect()) {
                throw new \Exception("Tidak dapat terhubung ke mesin absensi di {$this->ip}:{$this->port}");
            }

            $attendanceLogs = $zk->getAttendance();
            $zk->disconnect();

            foreach ($attendanceLogs as $log) {
                $logDate = date('Y-m-d', strtotime($log['timestamp']));

                // Only sync specified date
                if ($logDate !== $date) continue;

                // Find employee by NIP (stored as user ID in machine)
                $employee = Employee::where('nip', $log['uid'])->first();
                if (!$employee) continue;

                $time = date('H:i', strtotime($log['timestamp']));

                $existing = Attendance::where('employee_id', $employee->id)
                    ->where('tanggal', $logDate)
                    ->first();

                if ($existing) {
                    // Update keluar time if later
                    if (!$existing->jam_keluar || $time > $existing->jam_keluar) {
                        $existing->update(['jam_keluar' => $time, 'sumber' => 'mesin']);
                    }
                } else {
                    Attendance::create([
                        'employee_id' => $employee->id,
                        'tanggal'     => $logDate,
                        'jam_masuk'   => $time,
                        'status'      => 'hadir',
                        'sumber'      => 'mesin',
                    ]);
                    $synced++;
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return ['synced' => $synced, 'errors' => $errors];
    }

    /**
     * Test connection to machine
     */
    public function testConnection(): bool
    {
        try {
            $zk = new \Rats\Zkteco\Lib\ZKTeco($this->ip, $this->port);
            $connected = $zk->connect();
            if ($connected) $zk->disconnect();
            return $connected;
        } catch (\Exception $e) {
            return false;
        }
    }
}
