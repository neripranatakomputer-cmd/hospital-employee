@extends('layouts.app')
@section('title', 'Edit Karyawan')
@section('page-title', 'Edit Karyawan')
@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h5 class="mb-0 fw-bold">Edit Data Karyawan</h5>
        <p class="text-muted small mb-0">{{ $employee->nama_lengkap }} - NIP: {{ $employee->nip }}</p>
    </div>
</div>
<form method="POST" action="{{ route('employees.update', $employee) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#ep1" type="button"><i class="bi bi-person me-1"></i>Data Pribadi</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#ep2" type="button"><i class="bi bi-telephone me-1"></i>Data Kontak</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#ep3" type="button"><i class="bi bi-mortarboard me-1"></i>Data Pendidikan</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#ep4" type="button"><i class="bi bi-briefcase me-1"></i>Data Kepegawaian</button></li>
    </ul>
    @if($errors->any())
    <div class="alert alert-danger mb-4"><i class="bi bi-exclamation-triangle me-2"></i><strong>Terdapat kesalahan:</strong><ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    <div class="tab-content">
        <div class="tab-pane fade show active" id="ep1">
            <div class="card border-0 shadow-sm"><div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-person-circle me-2 text-primary"></i>Data Pribadi</h6></div>
            <div class="card-body">
                <div class="mb-4 text-center">
                    @if($employee->foto_profil)
                        <img src="{{ Storage::url($employee->foto_profil) }}" class="rounded-circle mb-3" id="photoPreview" width="100" height="100" style="object-fit:cover;border:3px solid #dee2e6">
                    @else
                        <div class="rounded-circle bg-light border d-inline-flex align-items-center justify-content-center mb-3" style="width:100px;height:100px" id="photoPlaceholder"><i class="bi bi-person-circle text-muted" style="font-size:48px"></i></div>
                        <img src="" class="rounded-circle mb-3 d-none" id="photoPreview" width="100" height="100" style="object-fit:cover">
                    @endif
                    <div><label class="btn btn-outline-primary btn-sm" for="foto_profil"><i class="bi bi-camera me-1"></i>Ubah Foto</label><input type="file" name="foto_profil" id="foto_profil" class="d-none" accept="image/*"></div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label fw-semibold">NIP *</label><input type="text" name="nip" value="{{ old('nip', $employee->nip) }}" class="form-control @error('nip') is-invalid @enderror" required>@error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label fw-semibold">NIK *</label><input type="text" name="nik" value="{{ old('nik', $employee->nik) }}" class="form-control @error('nik') is-invalid @enderror" maxlength="16" required>@error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Nama Lengkap *</label><input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $employee->nama_lengkap) }}" class="form-control @error('nama_lengkap') is-invalid @enderror" required>@error('nama_lengkap')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Nama Gelar</label><input type="text" name="nama_gelar" value="{{ old('nama_gelar', $employee->nama_gelar) }}" class="form-control" placeholder="dr., S.Kep."></div>
                    <div class="col-md-4"><label class="form-label fw-semibold">Jenis Kelamin *</label><select name="jenis_kelamin" class="form-select" required><option value="L" {{ old('jenis_kelamin', $employee->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option><option value="P" {{ old('jenis_kelamin', $employee->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option></select></div>
                    <p><div class="col-md-6"><label class="form-label fw-semibold">Tempat Lahir</label><input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $employee->tempat_lahir) }}" class="form-control" placeholder="Kota kelahiran"></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Tanggal Lahir</label><input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $employee->tanggal_lahir?->format('Y-m-d')) }}" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label fw-semibold">Golongan Darah</label><select name="golongan_darah" class="form-select"><option value="">Pilih...</option>
                        @foreach(['A','B','AB','O','A+','A-','B+','B-','AB+','AB-','O+','O-'] as $gd)
                            <option value="{{ $gd }}" {{ old('golongan_darah', $employee->golongan_darah) === $gd ? 'selected' : '' }}>{{ $gd }}</option>
                        @endforeach
                        </select></div>

<div class="col-md-6">
    <label class="form-label fw-semibold">Agama</label>
    <select name="agama" class="form-select">
        <option value="">Pilih...</option>
        @foreach(['Islam','Kristen Protestan','Kristen Katolik','Hindu','Buddha','Konghucu'] as $agama)
            <option value="{{ $agama }}" {{ old('agama', $employee->agama) === $agama ? 'selected' : '' }}>{{ $agama }}</option>
        @endforeach
    </select>
</div>




                    <div class="col-md-6"><label class="form-label fw-semibold">Status Pernikahan</label><select name="status_pernikahan" class="form-select">
                        <option value="">Pilih...</option>
                        <option value="belum_menikah" {{ old('status_pernikahan', $employee->status_pernikahan) === 'belum_menikah' ? 'selected' : '' }}>Belum Menikah</option>
                        <option value="menikah"       {{ old('status_pernikahan', $employee->status_pernikahan) === 'menikah'       ? 'selected' : '' }}>Menikah</option>
                        <option value="cerai_hidup"   {{ old('status_pernikahan', $employee->status_pernikahan) === 'cerai_hidup'   ? 'selected' : '' }}>Cerai Hidup</option>
                        <option value="cerai_mati"    {{ old('status_pernikahan', $employee->status_pernikahan) === 'cerai_mati'    ? 'selected' : '' }}>Cerai Mati</option>
                    </select></div>
                </div>
            </div></div>
        </div>
        <div class="tab-pane fade" id="ep2">
            <div class="card border-0 shadow-sm"><div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-telephone-fill me-2 text-success"></i>Data Kontak</h6></div>
            <div class="card-body"><div class="row g-3">
                <div class="col-md-6"><label class="form-label fw-semibold">No. HP</label><input type="text" name="no_hp" value="{{ old('no_hp', $employee->no_hp) }}" class="form-control"></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Email</label><input type="email" name="email" value="{{ old('email', $employee->email) }}" class="form-control"></div>
                <div class="col-12"><label class="form-label fw-semibold">Alamat</label><textarea name="alamat" rows="3" class="form-control">{{ old('alamat', $employee->alamat) }}</textarea></div>
            </div></div></div>
        </div>
        <div class="tab-pane fade" id="ep3">
            <div class="card border-0 shadow-sm"><div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-mortarboard-fill me-2 text-info"></i>Data Pendidikan</h6></div>
            <div class="card-body"><div class="row g-3">
                <div class="col-md-6"><label class="form-label fw-semibold">Pendidikan Terakhir</label><select name="pendidikan_terakhir" class="form-select"><option value="">Pilih...</option>@foreach(['SMA/SMK','D3','D4','S1','S2','S3','Profesi'] as $p)<option value="{{ $p }}" {{ old('pendidikan_terakhir',$employee->pendidikan_terakhir)===$p?'selected':'' }}>{{ $p }}</option>@endforeach</select></div>
                <div class="col-md-6">
    <label class="form-label fw-semibold">Program Studi</label>
    <input type="text" name="prodi_pendidikan"
        value="{{ old('prodi_pendidikan', $employee->prodi_pendidikan) }}"
        class="form-control"
        placeholder="Contoh: Keperawatan, Kedokteran Umum, dll">
</div>
                <div class="col-md-3"><label class="form-label fw-semibold">Tahun Lulus</label><input type="number" name="tahun_lulus_ijazah" value="{{ old('tahun_lulus_ijazah',$employee->tahun_lulus_ijazah) }}" class="form-control" min="1990" max="{{ date('Y') }}"></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Nomor Ijazah</label><input type="text" name="nomor_ijazah" value="{{ old('nomor_ijazah',$employee->nomor_ijazah) }}" class="form-control"></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Dokumen Ijazah</label>@if($employee->dokumen_ijazah)<div class="mb-2"><a href="{{ Storage::url($employee->dokumen_ijazah) }}" target="_blank" class="btn btn-sm btn-outline-info"><i class="bi bi-file-earmark-text me-1"></i>Lihat Ijazah</a></div>@endif<input type="file" name="dokumen_ijazah" class="form-control" accept=".pdf,.jpg,.jpeg,.png"><div class="form-text">Kosongkan jika tidak ingin mengubah</div></div>
            </div></div></div>
        </div>
        <div class="tab-pane fade" id="ep4">
            <div class="card border-0 shadow-sm"><div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-briefcase-fill me-2 text-warning"></i>Data Kepegawaian</h6></div>
            <div class="card-body"><div class="row g-3">
                <div class="col-md-6"><label class="form-label fw-semibold">Jabatan</label><input type="text" name="jabatan" value="{{ old('jabatan',$employee->jabatan) }}" class="form-control"></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Unit</label><input type="text" name="unit" value="{{ old('unit',$employee->unit) }}" class="form-control"></div>
                <div class="col-12"><hr><h6 class="text-muted small">STR</h6></div>
                <div class="col-md-3">
    <label class="form-label fw-semibold">Golongan Ruang</label>
    <select name="golongan_ruang" class="form-select">
        <option value="">Pilih...</option>
        @foreach(['I/a','I/b','I/c','I/d','II/a','II/b','II/c','II/d','III/a','III/b','III/c','III/d','IV/a','IV/b','IV/c','IV/d','IV/e'] as $gol)
            <option value="{{ $gol }}" {{ old('golongan_ruang', $employee->golongan_ruang) === $gol ? 'selected' : '' }}>{{ $gol }}</option>
        @endforeach
    </select>
</div>

<div class="col-md-3">
    <label class="form-label fw-semibold">TMT PNS</label>
    <input type="date" name="tmt_pns"
        value="{{ old('tmt_pns', $employee->tmt_pns?->format('Y-m-d')) }}"
        class="form-control @error('tmt_pns') is-invalid @enderror">
    <div class="form-text">Tanggal Mulai Tugas PNS</div>
    @error('tmt_pns')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
                <div class="col-md-6"><label class="form-label fw-semibold">Upload STR</label>@if($employee->str_file)<div class="mb-2"><a href="{{ Storage::url($employee->str_file) }}" target="_blank" class="btn btn-sm btn-outline-info"><i class="bi bi-file-earmark-text me-1"></i>Lihat STR</a></div>@endif<input type="file" name="str_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png"></div>
                <div class="col-12"><hr><h6 class="text-muted small">SIP</h6></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Upload SIP</label>@if($employee->sip_file)<div class="mb-2"><a href="{{ Storage::url($employee->sip_file) }}" target="_blank" class="btn btn-sm btn-outline-info"><i class="bi bi-file-earmark-text me-1"></i>Lihat SIP</a></div>@endif<input type="file" name="sip_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png"></div>
                <div class="col-md-3"><label class="form-label fw-semibold">TMT SIP</label><input type="date" name="tmt_sip" value="{{ old('tmt_sip',$employee->tmt_sip?->format('Y-m-d')) }}" class="form-control @error('tmt_sip') is-invalid @enderror">@error('tmt_sip')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-3"><label class="form-label fw-semibold">TAT SIP</label><input type="date" name="tat_sip" value="{{ old('tat_sip',$employee->tat_sip?->format('Y-m-d')) }}" class="form-control @error('tat_sip') is-invalid @enderror">@error('tat_sip')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                @if($employee->tat_sip)
                <div class="col-12">
                    @if($employee->sip_status==='kadaluarsa')<div class="alert alert-danger py-2 small"><i class="bi bi-x-circle me-1"></i>SIP kadaluarsa: {{ $employee->tat_sip->format('d/m/Y') }}</div>
                    @elseif($employee->sip_status==='hampir_kadaluarsa')<div class="alert alert-warning py-2 small"><i class="bi bi-exclamation-triangle me-1"></i>SIP akan kadaluarsa dalam <strong>{{ $employee->sip_days_left }} hari</strong></div>
                    @else<div class="alert alert-success py-2 small"><i class="bi bi-check-circle me-1"></i>SIP aktif hingga {{ $employee->tat_sip->format('d/m/Y') }}</div>@endif
                </div>
                @endif
            </div></div></div>
        </div>
    </div>
    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i>Simpan Perubahan</button>
        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
@endsection
@push('scripts')
<script>
document.getElementById('foto_profil').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (ev) => {
            const preview = document.getElementById('photoPreview');
            const placeholder = document.getElementById('photoPlaceholder');
            preview.src = ev.target.result;
            preview.classList.remove('d-none');
            if(placeholder) placeholder.classList.add('d-none');
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
