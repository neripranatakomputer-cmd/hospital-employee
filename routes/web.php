<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\UserController;

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
    Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::resource('/users', UserController::class)->except(['show']);

    // Employee Routes
    Route::get('/employees/sip-alerts', [EmployeeController::class, 'sipAlerts'])->name('employees.sip-alerts');
    Route::post('/employees/import', [EmployeeController::class, 'import'])->name('employees.import');
    Route::get('/employees/template', [EmployeeController::class, 'downloadTemplate'])->name('employees.template');
    Route::resource('/employees', EmployeeController::class);

    // Attendance Routes
    Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
    Route::post('/attendance/sync-machine', [AttendanceController::class, 'syncMachine'])->name('attendance.sync-machine');
    Route::resource('/attendance', AttendanceController::class)->except(['show']);

    // Download Rekap Absensi Routes
    Route::get('/attendance/report/download', [AttendanceController::class, 'downloadReport'])->name('attendance.report.download');
});
