<?php

namespace App\Http\Controllers;

use App\Models\Employee;

class NotificationController extends Controller
{
    public function index()
    {
        // SIP
        $sipKadaluarsa = Employee::expiredSip()->where('is_active', true)->get();
        $sipSegera     = Employee::expiringSip(30)->where('is_active', true)->get();

        // Kenaikan Pangkat
        $pangkatTerlambat = Employee::kenaikanPangkatTerlambat()->where('is_active', true)->get();
        $pangkatSegera    = Employee::kenaikanPangkatSegera(90)->where('is_active', true)->get();

        // Kenaikan Gaji Berkala
        $gajiTerlambat = Employee::kenaikanGajiTerlambat()->where('is_active', true)->get();
        $gajiSegera    = Employee::kenaikanGajiSegera(90)->where('is_active', true)->get();

        // Total semua notifikasi
        $totalAlert = $sipKadaluarsa->count()    + $sipSegera->count()
                    + $pangkatTerlambat->count() + $pangkatSegera->count()
                    + $gajiTerlambat->count()    + $gajiSegera->count();

        return view('notifications.index', compact(
            'sipKadaluarsa',    'sipSegera',
            'pangkatTerlambat', 'pangkatSegera',
            'gajiTerlambat',    'gajiSegera',
            'totalAlert'
        ));
    }
}