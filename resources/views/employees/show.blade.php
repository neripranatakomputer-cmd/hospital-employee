@extends('layouts.app')
@section('title', 'Detail Karyawan')
@section('page-title', 'Detail Karyawan')
@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i></a>
    <div class="flex-grow-1">
        <h5 class="mb-0 fw-bold">Detail Karyawan</h5>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <form method="POST" action="{{ route('employees.destroy', $employee) }}" onsubmit="return confirm('Hapus karyawan ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i>Hapus</button>
        </form>
    </div>
</div>

<div class="row g-4">
    <!-- Profile Card -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body pt-4">
                @if($employee->foto_profil)
                    <img src="{{ Storage::url($employee->foto_profil) }}" class="rounded-circle mb-3" width="120" height="120" style="object-fit:cover;border:4px solid #dee2e6">
                @else
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center text-primary fw-bold mb-3" style="width:120px;height:120px;font-size:48px">
                        {{ strtoupper(substr($employee->nama_lengkap, 0, 1)) }}
                    </div>
                @endif
                <h5 class="fw-bold mb-1">{{ $employee->nama_gelar ? $employee->nama_gelar . ' ' : '' }}{{ $employee->nama_lengkap }}</h5>
                <p class="text-muted mb-2">{{ $employee->jabatan }} - {{ $employee->unit }}</p>
                <span class="badge {{ $employee->jenis_kelamin === 'L' ? 'bg-primary' : 'bg-pink' }} bg-opacity-10 text-{{ $employee->jenis_kelamin === 'L' ? 'primary' : 'danger' }} px-3 py-2">
                    {{ $employee->jenis_kelamin === 'L' ? '♂ Laki-laki' : '♀ Perempuan' }}
                </span>

                <hr>

                <div class="text-start">
                    <div class="mb-2">
                        <small class="text-muted d-block">NIP</small>
                        <span class="fw-semibold">{{ $employee->nip }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">NIK</small>
                        <span class="fw-semibold">{{ $employee->nik }}</span>
                    </div>
                </div>

                <!-- SIP Status Badge -->
                <div class="mt-3">
                    @if(!$employee->tat_sip)
                        <span class="badge bg-secondary px-3 py-2">Tidak Ada SIP</span>
                    @elseif($employee->sip_status === 'kadaluarsa')
                        <div class="alert alert-danger py-2 small mb-0"><i class="bi bi-x-circle me-1"></i><strong>SIP Kadaluarsa</strong><br>{{ $employee->tat_sip->format('d F Y') }}</div>
                    @elseif($employee->sip_status === 'hampir_kadaluarsa')
                        <div class="alert alert-warning py-2 small mb-0"><i class="bi bi-exclamation-triangle me-1"></i><strong>SIP Hampir Kadaluarsa</strong><br>{{ $employee->sip_days_left }} hari lagi ({{ $employee->tat_sip->format('d F Y') }})</div>
                    @else
                        <div class="alert alert-success py-2 small mb-0"><i class="bi bi-check-circle me-1"></i><strong>SIP Aktif</strong><br>s.d. {{ $employee->tat_sip->format('d F Y') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Info -->
    <div class="col-md-8">
        <!-- Data Kontak -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-telephone-fill me-2 text-success"></i>Data Kontak</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4"><small class="text-muted">No. HP</small><div class="fw-semibold">{{ $employee->no_hp ?: '-' }}</div></div>
                    <div class="col-md-4"><small class="text-muted">Email</small><div class="fw-semibold">{{ $employee->email ?: '-' }}</div></div>
                    <div class="col-12"><small class="text-muted">Alamat</small><div class="fw-semibold">{{ $employee->alamat ?: '-' }}</div></div>
                </div>
            </div>
        </div>

        <!-- Data Pendidikan -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-mortarboard-fill me-2 text-info"></i>Data Pendidikan</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4"><small class="text-muted">Pendidikan Terakhir</small><div class="fw-semibold">{{ $employee->pendidikan_terakhir ?: '-' }}</div></div>
                    <div class="col-md-4"><small class="text-muted">Nomor Ijazah</small><div class="fw-semibold">{{ $employee->nomor_ijazah ?: '-' }}</div></div>
                    <div class="col-md-4"><small class="text-muted">Tahun Lulus</small><div class="fw-semibold">{{ $employee->tahun_lulus_ijazah ?: '-' }}</div></div>
                    @if($employee->dokumen_ijazah)
                    <div class="col-12"><a href="{{ Storage::url($employee->dokumen_ijazah) }}" target="_blank" class="btn btn-sm btn-outline-info"><i class="bi bi-file-earmark-text me-1"></i>Lihat Dokumen Ijazah</a></div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Data Kepegawaian -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-briefcase-fill me-2 text-warning"></i>Data Kepegawaian</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><small class="text-muted">Jabatan</small><div class="fw-semibold">{{ $employee->jabatan ?: '-' }}</div></div>
                    <div class="col-md-6"><small class="text-muted">Unit</small><div class="fw-semibold">{{ $employee->unit ?: '-' }}</div></div>

                    <div class="col-12"><hr class="my-1"></div>

                    <div class="col-md-4"><small class="text-muted">TMT SIP</small><div class="fw-semibold">{{ $employee->tmt_sip?->format('d F Y') ?: '-' }}</div></div>
                    <div class="col-md-4"><small class="text-muted">TAT SIP</small><div class="fw-semibold">{{ $employee->tat_sip?->format('d F Y') ?: '-' }}</div></div>

                    <div class="col-12 d-flex gap-2 flex-wrap">
                        @if($employee->str_file)
                            <a href="{{ Storage::url($employee->str_file) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-earmark-text me-1"></i>Lihat STR</a>
                        @endif
                        @if($employee->sip_file)
                            <a href="{{ Storage::url($employee->sip_file) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-earmark-text me-1"></i>Lihat SIP</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
