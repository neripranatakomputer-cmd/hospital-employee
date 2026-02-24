<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;

class DashboardController extends Controller
{
    public function index()
{
    $totalEmployees  = Employee::count();
    $activeEmployees = Employee::where('is_active', true)->count();
    $todayAttendance = \App\Models\Attendance::whereDate('tanggal', today())->where('status', 'hadir')->count();

    // Gabungan semua notifikasi
    $sipExpiring      = Employee::expiringSip(30)->count();
    $sipExpired       = Employee::expiredSip()->count();
    $pangkatAlert     = Employee::kenaikanPangkatTerlambat()->count() + Employee::kenaikanPangkatSegera(90)->count();
    $gajiAlert        = Employee::kenaikanGajiTerlambat()->count()    + Employee::kenaikanGajiSegera(90)->count();
    $totalAlert       = $sipExpired + $sipExpiring + $pangkatAlert + $gajiAlert;

    $recentEmployees  = Employee::latest()->take(5)->get();

    return view('dashboard', compact(
        'totalEmployees', 'activeEmployees', 'todayAttendance',
        'sipExpiring', 'sipExpired',
        'pangkatAlert', 'gajiAlert', 'totalAlert',
        'recentEmployees'
    ));
}
}
