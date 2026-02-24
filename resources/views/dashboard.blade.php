@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary bg-opacity-10">
                        <i class="bi bi-people-fill text-primary fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold text-dark">{{ $totalEmployees }}</div>
                        <div class="text-muted small">Total Karyawan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success bg-opacity-10">
                        <i class="bi bi-person-check-fill text-success fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold text-dark">{{ $activeEmployees }}</div>
                        <div class="text-muted small">Karyawan Aktif</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-info bg-opacity-10">
                        <i class="bi bi-calendar-check-fill text-info fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold text-dark">{{ $todayAttendance }}</div>
                        <div class="text-muted small">Hadir Hari Ini</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

     <div class="col-6 col-md-3">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 bg-danger bg-opacity-10">
                <i class="bi bi-arrow-up-circle-fill text-danger fs-4"></i>
            </div>
            <div>
                <div class="fs-3 fw-bold text-dark">{{ $totalAlert }}</div>
                <div class="text-muted small">Notif Kepegawaian</div>
            </div>
        </div>
    </div>
    </div>

</div>

<div class="row g-3">
    
    <!-- Recent Employees -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="bi bi-person-plus-fill text-primary me-2"></i>Karyawan Terbaru</h6>
                <a href="{{ route('employees.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @forelse($recentEmployees as $emp)
                <div class="d-flex align-items-center px-3 py-2 border-bottom">
                    <div class="flex-shrink-0 me-3">
                        @if($emp->foto_profil)
                            <img src="{{ Storage::url($emp->foto_profil) }}" class="rounded-circle" width="36" height="36" style="object-fit:cover">
                        @else
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" style="width:36px;height:36px;font-size:14px">
                                {{ strtoupper(substr($emp->nama_lengkap, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">{{ $emp->nama_lengkap }}</div>
                        <div class="text-muted" style="font-size:12px">NIP: {{ $emp->nip }} | {{ $emp->unit }}</div>
                    </div>
                    <a href="{{ route('employees.show', $emp) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-eye"></i>
                    </a>
                </div>
                @empty
                <div class="text-center text-muted py-4">Belum ada data karyawan</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
