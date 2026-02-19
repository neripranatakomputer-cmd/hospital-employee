@extends('layouts.app')

@section('title', 'Data Karyawan')
@section('page-title', 'Data Karyawan')

@section('content')
<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="mb-1 fw-bold">Data Karyawan</h5>
        <p class="text-muted small mb-0">Kelola seluruh data karyawan rumah sakit</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-file-earmark-excel me-1"></i>Import Excel
        </button>
        <a href="{{ route('employees.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i>Tambah Karyawan
        </a>
    </div>
</div>

<!-- SIP Alert Banner -->
@if($sipExpiringCount > 0 || $sipExpiredCount > 0)
<div class="alert alert-warning d-flex align-items-center justify-content-between mb-4" role="alert">
    <div>
        <i class="bi bi-bell-fill me-2"></i>
        @if($sipExpiredCount > 0)
            <strong>{{ $sipExpiredCount }} SIP kadaluarsa</strong>
        @endif
        @if($sipExpiringCount > 0 && $sipExpiredCount > 0) dan @endif
        @if($sipExpiringCount > 0)
            <strong>{{ $sipExpiringCount }} SIP akan kadaluarsa</strong> dalam 30 hari
        @endif
    </div>
    <a href="{{ route('employees.sip-alerts') }}" class="btn btn-warning btn-sm">Lihat Detail</a>
</div>
@endif

<!-- Filter Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small text-muted">Cari</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control" placeholder="Nama, NIP, NIK, Jabatan...">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">Unit</label>
                <select name="unit" class="form-select select2">
                    <option value="">Semua Unit</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit }}" {{ request('unit') == $unit ? 'selected' : '' }}>{{ $unit }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">Jabatan</label>
                <select name="jabatan" class="form-select select2">
                    <option value="">Semua Jabatan</option>
                    @foreach($jabatans as $jabatan)
                        <option value="{{ $jabatan }}" {{ request('jabatan') == $jabatan ? 'selected' : '' }}>{{ $jabatan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Karyawan</th>
                        <th>NIP / NIK</th>
                        <th>Jabatan & Unit</th>
                        <th>Kontak</th>
                        <th>Status SIP</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $i => $emp)
                    <tr>
                        <td class="text-muted small">{{ $employees->firstItem() + $i }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="flex-shrink-0">
                                    @if($emp->foto_profil)
                                        <img src="{{ Storage::url($emp->foto_profil) }}" class="rounded-circle" width="40" height="40" style="object-fit:cover">
                                    @else
                                        <div class="rounded-circle bg-primary bg-opacity-15 d-flex align-items-center justify-content-center text-primary fw-bold" style="width:40px;height:40px">
                                            {{ strtoupper(substr($emp->nama_lengkap, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $emp->nama_gelar ? $emp->nama_gelar . ' ' : '' }}{{ $emp->nama_lengkap }}</div>
                                    <div class="text-muted small">{{ $emp->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small">NIP: <span class="fw-semibold">{{ $emp->nip }}</span></div>
                            <div class="small text-muted">NIK: {{ $emp->nik }}</div>
                        </td>
                        <td>
                            <div class="small fw-semibold">{{ $emp->jabatan ?: '-' }}</div>
                            <div class="small text-muted">{{ $emp->unit ?: '-' }}</div>
                        </td>
                        <td>
                            <div class="small">{{ $emp->no_hp ?: '-' }}</div>
                            <div class="small text-muted text-truncate" style="max-width:150px">{{ $emp->email ?: '' }}</div>
                        </td>
                        <td>
                            @if(!$emp->tat_sip)
                                <span class="badge bg-secondary">Tidak Ada SIP</span>
                            @elseif($emp->sip_status === 'kadaluarsa')
                                <span class="badge bg-danger">Kadaluarsa</span>
                                <div class="text-muted" style="font-size:11px">{{ $emp->tat_sip->format('d/m/Y') }}</div>
                            @elseif($emp->sip_status === 'hampir_kadaluarsa')
                                <span class="badge bg-warning text-dark">{{ $emp->sip_days_left }} hari lagi</span>
                                <div class="text-muted" style="font-size:11px">{{ $emp->tat_sip->format('d/m/Y') }}</div>
                            @else
                                <span class="badge bg-success">Aktif</span>
                                <div class="text-muted" style="font-size:11px">{{ $emp->tat_sip->format('d/m/Y') }}</div>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('employees.show', $emp) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('employees.edit', $emp) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('employees.destroy', $emp) }}"
                                    onsubmit="return confirm('Hapus karyawan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
                            Tidak ada data karyawan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($employees->hasPages())
    <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center">
        <small class="text-muted">
            Menampilkan {{ $employees->firstItem() }}-{{ $employees->lastItem() }} dari {{ $employees->total() }} data
        </small>
        {{ $employees->links() }}
    </div>
    @endif
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-file-earmark-excel me-2 text-success"></i>Import Karyawan dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('employees.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle me-1"></i>
                        Download template Excel terlebih dahulu, isi data sesuai format, lalu upload.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">File Excel</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text">Format: .xlsx, .xls, .csv (maks 5MB)</div>
                    </div>
                    @if(session('import_errors'))
                    <div class="alert alert-warning small">
                        <strong>Baris yang gagal:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach(session('import_errors') as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <a href="{{ route('employees.template') }}" class="btn btn-outline-success me-auto">
                        <i class="bi bi-download me-1"></i>Download Template
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i>Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
