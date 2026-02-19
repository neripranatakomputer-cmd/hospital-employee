@extends('layouts.app')
@section('title', 'Notifikasi SIP')
@section('page-title', 'Notifikasi SIP')
@section('content')
<div class="d-flex align-items-center mb-4">
    <div>
        <h5 class="mb-0 fw-bold">Notifikasi SIP</h5>
        <p class="text-muted small mb-0">Monitor masa berlaku Surat Izin Praktik karyawan</p>
    </div>
</div>

<!-- Expired SIP -->
<div class="card border-0 shadow-sm border-danger mb-4" style="border-left: 4px solid #dc3545 !important">
    <div class="card-header bg-white d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold text-danger"><i class="bi bi-x-circle-fill me-2"></i>SIP Kadaluarsa ({{ $expired->count() }})</h6>
    </div>
    <div class="card-body p-0">
        @forelse($expired as $emp)
        <div class="d-flex align-items-center px-4 py-3 border-bottom">
            <div class="me-3">
                @if($emp->foto_profil)
                    <img src="{{ Storage::url($emp->foto_profil) }}" class="rounded-circle" width="48" height="48" style="object-fit:cover">
                @else
                    <div class="rounded-circle bg-danger bg-opacity-15 d-flex align-items-center justify-content-center text-danger fw-bold" style="width:48px;height:48px">{{ strtoupper(substr($emp->nama_lengkap,0,1)) }}</div>
                @endif
            </div>
            <div class="flex-grow-1">
                <div class="fw-semibold">{{ $emp->nama_lengkap }}</div>
                <div class="text-muted small">{{ $emp->jabatan }} | {{ $emp->unit }} | NIP: {{ $emp->nip }}</div>
                <div class="small text-danger mt-1"><i class="bi bi-calendar-x me-1"></i>Kadaluarsa sejak: <strong>{{ $emp->tat_sip->format('d F Y') }}</strong> ({{ abs($emp->sip_days_left) }} hari yang lalu)</div>
            </div>
            <a href="{{ route('employees.edit', $emp) }}#ep4" class="btn btn-sm btn-danger">Perbarui SIP</a>
        </div>
        @empty
        <div class="text-center text-muted py-4"><i class="bi bi-check-circle text-success fs-2 d-block mb-2"></i>Tidak ada SIP yang kadaluarsa</div>
        @endforelse
    </div>
</div>

<!-- Expiring SIP -->
<div class="card border-0 shadow-sm" style="border-left: 4px solid #ffc107 !important">
    <div class="card-header bg-white d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold text-warning"><i class="bi bi-exclamation-triangle-fill me-2"></i>SIP Akan Kadaluarsa dalam 30 Hari ({{ $expiring->count() }})</h6>
    </div>
    <div class="card-body p-0">
        @forelse($expiring as $emp)
        <div class="d-flex align-items-center px-4 py-3 border-bottom">
            <div class="me-3">
                @if($emp->foto_profil)
                    <img src="{{ Storage::url($emp->foto_profil) }}" class="rounded-circle" width="48" height="48" style="object-fit:cover">
                @else
                    <div class="rounded-circle bg-warning bg-opacity-15 d-flex align-items-center justify-content-center text-warning fw-bold" style="width:48px;height:48px">{{ strtoupper(substr($emp->nama_lengkap,0,1)) }}</div>
                @endif
            </div>
            <div class="flex-grow-1">
                <div class="fw-semibold">{{ $emp->nama_lengkap }}</div>
                <div class="text-muted small">{{ $emp->jabatan }} | {{ $emp->unit }} | NIP: {{ $emp->nip }}</div>
                <div class="small text-warning mt-1">
                    <i class="bi bi-clock me-1"></i>Berakhir: <strong>{{ $emp->tat_sip->format('d F Y') }}</strong>
                    @if($emp->sip_days_left <= 7)
                        <span class="badge bg-danger ms-1">{{ $emp->sip_days_left }} hari lagi!</span>
                    @elseif($emp->sip_days_left <= 14)
                        <span class="badge bg-warning text-dark ms-1">{{ $emp->sip_days_left }} hari lagi</span>
                    @else
                        <span class="badge bg-info ms-1">{{ $emp->sip_days_left }} hari lagi</span>
                    @endif
                </div>
            </div>
            <a href="{{ route('employees.edit', $emp) }}" class="btn btn-sm btn-warning">Perbarui SIP</a>
        </div>
        @empty
        <div class="text-center text-muted py-4"><i class="bi bi-check-circle text-success fs-2 d-block mb-2"></i>Tidak ada SIP yang akan kadaluarsa dalam 30 hari</div>
        @endforelse
    </div>
</div>
@endsection
