@extends('layouts.app')
@section('title', 'Notifikasi Kepegawaian')
@section('page-title', 'Notifikasi Kepegawaian')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="mb-0 fw-bold">Notifikasi Kepegawaian</h5>
        <p class="text-muted small mb-0">Monitor SIP, kenaikan pangkat, dan kenaikan gaji berkala</p>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-2">
        <div class="card border-0 shadow-sm text-center py-3 h-100">
            <div class="fs-3 fw-bold text-danger">{{ $sipKadaluarsa->count() }}</div>
            <div class="text-muted" style="font-size:11px">SIP Kadaluarsa</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-0 shadow-sm text-center py-3 h-100">
            <div class="fs-3 fw-bold text-warning">{{ $sipSegera->count() }}</div>
            <div class="text-muted" style="font-size:11px">SIP Segera</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-0 shadow-sm text-center py-3 h-100">
            <div class="fs-3 fw-bold text-danger">{{ $pangkatTerlambat->count() }}</div>
            <div class="text-muted" style="font-size:11px">Pangkat Terlambat</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-0 shadow-sm text-center py-3 h-100">
            <div class="fs-3 fw-bold text-warning">{{ $pangkatSegera->count() }}</div>
            <div class="text-muted" style="font-size:11px">Pangkat Segera</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-0 shadow-sm text-center py-3 h-100">
            <div class="fs-3 fw-bold text-danger">{{ $gajiTerlambat->count() }}</div>
            <div class="text-muted" style="font-size:11px">Gaji Berkala Terlambat</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-0 shadow-sm text-center py-3 h-100">
            <div class="fs-3 fw-bold text-warning">{{ $gajiSegera->count() }}</div>
            <div class="text-muted" style="font-size:11px">Gaji Berkala Segera</div>
        </div>
    </div>
</div>

{{-- Helper macro untuk tiap section --}}
@php
    $sections = [
        [
            'color'   => 'danger',
            'icon'    => 'shield-x',
            'title'   => 'SIP Kadaluarsa',
            'data'    => $sipKadaluarsa,
            'type'    => 'sip_expired',
        ],
        [
            'color'   => 'warning',
            'icon'    => 'shield-exclamation',
            'title'   => 'SIP Akan Kadaluarsa (30 hari)',
            'data'    => $sipSegera,
            'type'    => 'sip_segera',
        ],
        [
            'color'   => 'danger',
            'icon'    => 'arrow-up-circle-fill',
            'title'   => 'Kenaikan Pangkat Terlambat (> 3 tahun)',
            'data'    => $pangkatTerlambat,
            'type'    => 'pangkat_terlambat',
        ],
        [
            'color'   => 'warning',
            'icon'    => 'arrow-up-circle',
            'title'   => 'Kenaikan Pangkat dalam 3 Bulan',
            'data'    => $pangkatSegera,
            'type'    => 'pangkat_segera',
        ],
        [
            'color'   => 'danger',
            'icon'    => 'cash-coin',
            'title'   => 'Kenaikan Gaji Berkala Terlambat (> 2 tahun)',
            'data'    => $gajiTerlambat,
            'type'    => 'gaji_terlambat',
        ],
        [
            'color'   => 'warning',
            'icon'    => 'cash-coin',
            'title'   => 'Kenaikan Gaji Berkala dalam 3 Bulan',
            'data'    => $gajiSegera,
            'type'    => 'gaji_segera',
        ],
    ];
@endphp

@foreach($sections as $section)
<div class="card border-0 shadow-sm mb-3" style="border-left: 4px solid {{ $section['color'] === 'danger' ? '#dc3545' : '#ffc107' }} !important">
    <div class="card-header bg-white d-flex align-items-center justify-content-between py-2"
        role="button" data-bs-toggle="collapse"
        data-bs-target="#section-{{ $loop->index }}"
        style="cursor:pointer">
        <h6 class="mb-0 fw-bold text-{{ $section['color'] }}">
            <i class="bi bi-{{ $section['icon'] }} me-2"></i>
            {{ $section['title'] }}
            <span class="badge bg-{{ $section['color'] }} ms-2">{{ $section['data']->count() }}</span>
        </h6>
        <i class="bi bi-chevron-down text-muted"></i>
    </div>

    <div class="collapse show" id="section-{{ $loop->index }}">
        <div class="card-body p-0">
            @forelse($section['data'] as $emp)
            <div class="d-flex align-items-center px-4 py-3 border-bottom">
                {{-- Avatar --}}
                <div class="me-3 flex-shrink-0">
                    @if($emp->foto_profil)
                        <img src="{{ Storage::url($emp->foto_profil) }}"
                            class="rounded-circle" width="44" height="44" style="object-fit:cover">
                    @else
                        <div class="rounded-circle bg-{{ $section['color'] }} bg-opacity-15
                            d-flex align-items-center justify-content-center
                            text-{{ $section['color'] }} fw-bold"
                            style="width:44px;height:44px;font-size:16px">
                            {{ strtoupper(substr($emp->nama_lengkap, 0, 1)) }}
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-grow-1">
                    <div class="fw-semibold">{{ $emp->nama_lengkap }}</div>
                    <div class="text-muted small">
                        {{ $emp->jabatan ?? '-' }} | {{ $emp->unit ?? '-' }}
                        @if($emp->golongan_ruang) | Gol. <strong>{{ $emp->golongan_ruang }}</strong> @endif
                        | NIP: {{ $emp->nip }}
                    </div>

                    {{-- Detail per tipe --}}
                    <div class="mt-1" style="font-size:12px">
                        @if($section['type'] === 'sip_expired')
                            <span class="text-danger">
                                <i class="bi bi-calendar-x me-1"></i>
                                SIP kadaluarsa sejak <strong>{{ $emp->tat_sip->format('d/m/Y') }}</strong>
                                ({{ abs($emp->sip_days_left) }} hari yang lalu)
                            </span>

                        @elseif($section['type'] === 'sip_segera')
                            <span class="text-warning">
                                <i class="bi bi-clock me-1"></i>
                                SIP berakhir <strong>{{ $emp->tat_sip->format('d/m/Y') }}</strong>
                            </span>
                            @if($emp->sip_days_left <= 7)
                                <span class="badge bg-danger ms-1">{{ $emp->sip_days_left }} hari lagi!</span>
                            @else
                                <span class="badge bg-warning text-dark ms-1">{{ $emp->sip_days_left }} hari lagi</span>
                            @endif

                        @elseif($section['type'] === 'pangkat_terlambat')
                            <span class="text-danger">
                                <i class="bi bi-calendar-x me-1"></i>
                                TMT Golongan: <strong>{{ $emp->tmt_golongan->format('d/m/Y') }}</strong>
                                | Due: <strong>{{ $emp->kenaikan_pangkat_due_date->format('d/m/Y') }}</strong>
                                | Terlambat <strong>{{ abs($emp->kenaikan_pangkat_days_left) }} hari</strong>
                            </span>

                        @elseif($section['type'] === 'pangkat_segera')
                            <span class="text-warning">
                                <i class="bi bi-clock me-1"></i>
                                Due kenaikan pangkat: <strong>{{ $emp->kenaikan_pangkat_due_date->format('d/m/Y') }}</strong>
                            </span>
                            @if($emp->kenaikan_pangkat_days_left <= 30)
                                <span class="badge bg-danger ms-1">{{ $emp->kenaikan_pangkat_days_left }} hari lagi!</span>
                            @else
                                <span class="badge bg-warning text-dark ms-1">{{ $emp->kenaikan_pangkat_days_left }} hari lagi</span>
                            @endif

                        @elseif($section['type'] === 'gaji_terlambat')
                            <span class="text-danger">
                                <i class="bi bi-calendar-x me-1"></i>
                                TMT Golongan: <strong>{{ $emp->tmt_golongan->format('d/m/Y') }}</strong>
                                | Due: <strong>{{ $emp->kenaikan_gaji_due_date->format('d/m/Y') }}</strong>
                                | Terlambat <strong>{{ abs($emp->kenaikan_gaji_days_left) }} hari</strong>
                            </span>

                        @elseif($section['type'] === 'gaji_segera')
                            <span class="text-warning">
                                <i class="bi bi-clock me-1"></i>
                                Due kenaikan gaji berkala: <strong>{{ $emp->kenaikan_gaji_due_date->format('d/m/Y') }}</strong>
                            </span>
                            @if($emp->kenaikan_gaji_days_left <= 30)
                                <span class="badge bg-danger ms-1">{{ $emp->kenaikan_gaji_days_left }} hari lagi!</span>
                            @else
                                <span class="badge bg-warning text-dark ms-1">{{ $emp->kenaikan_gaji_days_left }} hari lagi</span>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Tombol aksi --}}
                <a href="{{ route('employees.edit', $emp) }}"
                    class="btn btn-sm btn-outline-{{ $section['color'] }} ms-3 flex-shrink-0">
                    <i class="bi bi-pencil me-1"></i>Update
                </a>
            </div>
            @empty
            <div class="text-center text-muted py-3">
                <i class="bi bi-check-circle text-success me-1"></i>
                Tidak ada data
            </div>
            @endforelse
        </div>
    </div>
</div>
@endforeach

@endsection