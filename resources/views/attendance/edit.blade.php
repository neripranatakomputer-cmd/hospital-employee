@extends('layouts.app')
@section('title', 'Edit Absensi')
@section('page-title', 'Edit Absensi')
@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i></a>
    <h5 class="mb-0 fw-bold">Edit Absensi</h5>
</div>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-calendar-check me-2 text-primary"></i>Edit Data Absensi</h6></div>
            <div class="card-body">
                <div class="alert alert-light border mb-3">
                    <strong>{{ $attendance->employee->nama_lengkap }}</strong> - {{ $attendance->tanggal->format('d F Y') }}
                    <span class="badge bg-{{ $attendance->sumber === 'mesin' ? 'primary' : 'secondary' }} ms-2">{{ $attendance->sumber === 'mesin' ? '🤖 Mesin' : '✍️ Manual' }}</span>
                </div>
                <form method="POST" action="{{ route('attendance.update', $attendance) }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status *</label>
                            <select name="status" class="form-select" required>
                                @foreach(['hadir','izin','sakit','alpha','cuti'] as $s)
                                    <option value="{{ $s }}" {{ old('status',$attendance->status)===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Jam Masuk</label>
                            <input type="time" name="jam_masuk" value="{{ old('jam_masuk',$attendance->jam_masuk) }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Jam Keluar</label>
                            <input type="time" name="jam_keluar" value="{{ old('jam_keluar',$attendance->jam_keluar) }}" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea name="keterangan" rows="2" class="form-control">{{ old('keterangan',$attendance->keterangan) }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i>Simpan</button>
                        <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
