<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('is_active', true)->count();
        $todayAttendance = Attendance::whereDate('tanggal', today())->where('status', 'hadir')->count();
        $sipExpiring = Employee::expiringSip(30)->count();
        $sipExpired = Employee::expiredSip()->count();

        $recentEmployees = Employee::latest()->take(5)->get();
        $sipAlerts = Employee::expiringSip(30)->orWhere(function($q) {
            $q->whereNotNull('tat_sip')->whereDate('tat_sip', '<', now());
        })->take(10)->get();

        return view('dashboard', compact(
            'totalEmployees', 'activeEmployees', 'todayAttendance',
            'sipExpiring', 'sipExpired', 'recentEmployees', 'sipAlerts'
        ));
    }
}
