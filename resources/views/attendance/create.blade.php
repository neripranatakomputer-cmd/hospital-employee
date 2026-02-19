@extends('layouts.app')
@section('title', 'Input Absensi')
@section('page-title', 'Input Absensi')
@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i></a>
    <h5 class="mb-0 fw-bold">Input Absensi Manual</h5>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-calendar-check me-2 text-primary"></i>Form Absensi</h6></div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger mb-3"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                @endif

                <form method="POST" action="{{ route('attendance.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Karyawan <span class="text-danger">*</span></label>
                            <select name="employee_id" class="form-select select2 @error('employee_id') is-invalid @enderror" required>
                                <option value="">Pilih Karyawan...</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->nama_lengkap }} - {{ $emp->jabatan }} ({{ $emp->unit }})
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" value="{{ old('tanggal', today()->format('Y-m-d')) }}" class="form-control @error('tanggal') is-invalid @enderror" required>
                            @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" id="statusSelect" class="form-select" required>
                                <option value="hadir" {{ old('status') === 'hadir' ? 'selected' : '' }}>Hadir</option>
                                <option value="izin" {{ old('status') === 'izin' ? 'selected' : '' }}>Izin</option>
                                <option value="sakit" {{ old('status') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="alpha" {{ old('status') === 'alpha' ? 'selected' : '' }}>Alpha</option>
                                <option value="cuti" {{ old('status') === 'cuti' ? 'selected' : '' }}>Cuti</option>
                            </select>
                        </div>

                        <div class="col-md-6" id="jamMasukField">
                            <label class="form-label fw-semibold">Jam Masuk</label>
                            <input type="time" name="jam_masuk" value="{{ old('jam_masuk') }}" class="form-control">
                        </div>

                        <div class="col-md-6" id="jamKeluarField">
                            <label class="form-label fw-semibold">Jam Keluar</label>
                            <input type="time" name="jam_keluar" value="{{ old('jam_keluar') }}" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea name="keterangan" rows="2" class="form-control" placeholder="Opsional...">{{ old('keterangan') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i>Simpan Absensi</button>
                        <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('statusSelect').addEventListener('change', function() {
    const jamFields = document.querySelectorAll('#jamMasukField, #jamKeluarField');
    if (this.value !== 'hadir') {
        jamFields.forEach(f => f.style.opacity = '0.5');
    } else {
        jamFields.forEach(f => f.style.opacity = '1');
    }
});
</script>
@endpush
