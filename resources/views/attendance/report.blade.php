@extends('layouts.app')
@section('title', 'Laporan Absensi')
@section('page-title', 'Laporan Absensi')
@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="mb-0 fw-bold">Laporan Absensi Bulanan</h5>
        <p class="text-muted small mb-0">Rekap kehadiran per bulan</p>
    </div>
</div>

<!-- Month Filter dan Tombol Download Rekap Absensi-->

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="d-flex gap-3 align-items-end">
            <div>
                <label class="form-label small text-muted">Bulan</label>
                <input type="month" name="month" value="{{ $month }}" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Tampilkan</button>

            {{-- TAMBAHKAN INI --}}
            <a href="{{ route('attendance.report.download', ['month' => $month]) }}"
               class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-1"></i>Download Excel
            </a>
        </form>
    </div>
</div>

<!-- Summary Stats -->
<div class="row g-3 mb-4">
    @php
        $allAttendances = $employees->flatMap->attendances;
        $totalHadir = $allAttendances->where('status', 'hadir')->count();
        $totalIzin = $allAttendances->where('status', 'izin')->count();
        $totalSakit = $allAttendances->where('status', 'sakit')->count();
        $totalCuti = $allAttendances->where('status', 'cuti')->count();
        $totalAlpha = $allAttendances->where('status', 'alpha')->count();
    @endphp
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center py-3"><div class="fs-2 fw-bold text-success">{{ $totalHadir }}</div><div class="text-muted small">Total Hadir</div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center py-3"><div class="fs-2 fw-bold text-info">{{ $totalIzin }}</div><div class="text-muted small">Total Izin</div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center py-3"><div class="fs-2 fw-bold text-warning">{{ $totalSakit }}</div><div class="text-muted small">Total Sakit</div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center py-3"><div class="fs-2 fw-bold text-danger">{{ $totalCuti }}</div><div class="text-muted small">Total Cuti</div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center py-3"><div class="fs-2 fw-bold text-danger">{{ $totalAlpha }}</div><div class="text-muted small">Total Alpha</div></div></div>
</div>

<!-- Attendance Grid -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h6 class="mb-0 fw-bold">Rekap {{ \Carbon\Carbon::create($year, $mon)->translatedFormat('F Y') }}</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle mb-0" style="min-width: 900px">
                <thead class="table-light">
                    <tr>
                        <th style="min-width:180px">Karyawan</th>
                        @for($d = 1; $d <= $daysInMonth; $d++)
                            <th class="text-center" style="width:32px">{{ $d }}</th>
                        @endfor
                        <th class="text-center">H</th>
                        <th class="text-center">I</th>
                        <th class="text-center">S</th>
                        <th class="text-center">C</th>
                        <th class="text-center">A</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $emp)
                    @php
                        $attByDate = $emp->attendances->keyBy(fn($a) => $a->tanggal->format('d'));
                        $h = $emp->attendances->where('status','hadir')->count();
                        $iz = $emp->attendances->where('status','izin')->count();
                        $s = $emp->attendances->where('status','sakit')->count();
                        $c = $emp->attendances->where('status','cuti')->count();
                        $a = $emp->attendances->where('status','alpha')->count();
                    @endphp
                    <tr>
                        <td class="fw-semibold small ps-3">{{ $emp->nama_lengkap }}<br><span class="text-muted fw-normal">{{ $emp->jabatan }}</span></td>
                        @for($d = 1; $d <= $daysInMonth; $d++)
                            @php
                                $date = sprintf('%02d', $d);
                                $att = $attByDate[$date] ?? null;
                                $dayOfWeek = \Carbon\Carbon::create($year, $mon, $d)->dayOfWeek;
                                $isWeekend = in_array($dayOfWeek, [0, 6]);
                            @endphp
                            <td class="text-center p-1 {{ $isWeekend ? 'bg-light' : '' }}" style="font-size:10px">
                                @if($att)
                                    @php $icons = ['hadir'=>'✓','izin'=>'I','sakit'=>'S','alpha'=>'A','cuti'=>'C'] @endphp
                                    @php $colors = ['hadir'=>'success','izin'=>'info','sakit'=>'warning','alpha'=>'danger','cuti'=>'secondary'] @endphp
                                    <span class="text-{{ $colors[$att->status] ?? 'secondary' }} fw-bold">{{ $icons[$att->status] ?? '?' }}</span>
                                @elseif($isWeekend)
                                    <span class="text-muted">-</span>
                                @else
                                    <span class="text-light">·</span>
                                @endif
                            </td>
                        @endfor
                        <td class="text-center fw-bold text-success small">{{ $h }}</td>
                        <td class="text-center fw-bold text-info small">{{ $iz }}</td>
                        <td class="text-center fw-bold text-warning small">{{ $s }}</td>
                        <td class="text-center fw-bold text-danger small">{{ $c }}</td>
                        <td class="text-center fw-bold text-danger small">{{ $a }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        <small class="text-muted"><strong>Keterangan:</strong> ✓ = Hadir, I = Izin, S = Sakit, A = Alpha, C = Cuti | H = Hadir, I = Izin, S = Sakit, A = Alpha</small>
    </div>
</div>
@endsection
