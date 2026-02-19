@extends('layouts.app')
@section('title', 'Data Absensi')
@section('page-title', 'Data Absensi')
@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="mb-0 fw-bold">Data Absensi</h5>
        <p class="text-muted small mb-0">Kelola kehadiran karyawan</p>
    </div>
    <div class="d-flex gap-2">
        <form method="POST" action="{{ route('attendance.sync-machine') }}" class="d-flex gap-2">
            @csrf
            <input type="date" name="date" value="{{ today()->format('Y-m-d') }}" class="form-control form-control-sm" style="width:160px">
            <button type="submit" class="btn btn-outline-secondary"
                onclick="return confirm('Sync data absensi dari mesin fingerprint?')">
                <i class="bi bi-arrow-repeat me-1"></i>Sync Mesin
            </button>
            <a href="{{ route('attendance.test-machine') }}" target="_blank"
                class="btn btn-outline-info" title="Test koneksi mesin">
                <i class="bi bi-wifi"></i>
            </a>
        </form>
        <a href="{{ route('attendance.create') }}" class="btn btn-primary"><i class="bi bi-plus me-1"></i>Input Absensi</a>
    </div>
</div>

<!-- Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted">Tanggal</label>
                <input type="date" name="tanggal" value="{{ request('tanggal', today()->format('Y-m-d')) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted">Karyawan</label>
                <select name="employee_id" class="form-select select2">
                    <option value="">Semua Karyawan</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->nama_lengkap }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ request('status') === 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="izin" {{ request('status') === 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="sakit" {{ request('status') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="alpha" {{ request('status') === 'alpha' ? 'selected' : '' }}>Alpha</option>
                    <option value="cuti" {{ request('status') === 'cuti' ? 'selected' : '' }}>Cuti</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                        <th>#</th>
                        <th>Karyawan</th>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Status</th>
                        <th>Sumber</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $i => $att)
                    <tr>
                        <td class="text-muted small">{{ $attendances->firstItem() + $i }}</td>
                        <td>
                            <div class="fw-semibold small">{{ $att->employee?->nama_lengkap ?? '(Karyawan dihapus)' }}</div>
                            <div class="text-muted" style="font-size:12px">{{ $att->employee?->jabatan ?? '-' }}</div>
                        </td>
                        <td><span class="small">{{ $att->tanggal->format('d/m/Y') }}</span></td>
                        
                        <td>
    @if($att->jam_masuk)
        <span class="badge text-success border border-success">
            <i class="bi bi-clock me-1"></i>{{ $att->jam_masuk }}
        </span>
    @else
        <span class="text-muted small">-</span>
    @endif
</td>
<td>
    @if($att->jam_keluar)
        <span class="badge text-secondary border border-secondary">
            <i class="bi bi-clock-history me-1"></i>{{ $att->jam_keluar }}
        </span>
    @else
        <span class="text-muted small">-</span>
    @endif
</td>

                        <td>
                            @php $statusColors = ['hadir'=>'success','izin'=>'info','sakit'=>'warning','alpha'=>'danger','cuti'=>'secondary'] @endphp
                            <span class="badge bg-{{ $statusColors[$att->status] ?? 'secondary' }}">{{ ucfirst($att->status) }}</span>
                        </td>
                        <td><span class="badge bg-{{ $att->sumber === 'mesin' ? 'primary' : 'light text-dark' }} small">{{ $att->sumber === 'mesin' ? '🤖 Mesin' : '✍️ Manual' }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('attendance.edit', $att) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('attendance.destroy', $att) }}" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-5 text-muted"><i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-25"></i>Tidak ada data absensi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($attendances->hasPages())
    <div class="card-footer bg-white border-top">{{ $attendances->links() }}</div>
    @endif
</div>
@endsection
