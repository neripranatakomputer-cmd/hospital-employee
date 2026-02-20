@extends('layouts.app')

@section('title', isset($employee) ? 'Edit Karyawan' : 'Tambah Karyawan')
@section('page-title', isset($employee) ? 'Edit Karyawan' : 'Tambah Karyawan')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h5 class="mb-0 fw-bold">{{ isset($employee) ? 'Edit Data Karyawan' : 'Tambah Karyawan Baru' }}</h5>
        <p class="text-muted small mb-0">Lengkapi semua informasi karyawan</p>
    </div>
</div>

<form method="POST"
    action="{{ isset($employee) ? route('employees.update', $employee) : route('employees.store') }}"
    enctype="multipart/form-data"
    id="employeeForm">
    @csrf
    @if(isset($employee)) @method('PUT') @endif

    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-4" id="formTabs">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-pribadi" type="button">
                <i class="bi bi-person me-1"></i>Data Pribadi
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-kontak" type="button">
                <i class="bi bi-telephone me-1"></i>Data Kontak
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-pendidikan" type="button">
                <i class="bi bi-mortarboard me-1"></i>Data Pendidikan
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-kepegawaian" type="button">
                <i class="bi bi-briefcase me-1"></i>Data Kepegawaian
            </button>
        </li>
    </ul>

    @if($errors->any())
    <div class="alert alert-danger mb-4">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Terdapat kesalahan input:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="tab-content">
        <!-- Tab 1: Data Pribadi -->
        <div class="tab-pane fade show active" id="tab-pribadi">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-person-circle me-2 text-primary"></i>Data Pribadi</h6>
                </div>
                <div class="card-body">
                    <!-- Photo Upload -->
                    <div class="mb-4 text-center">
                        <div id="photoPreviewContainer">
                            @if(isset($employee) && $employee->foto_profil)
                                <img src="{{ Storage::url($employee->foto_profil) }}" class="photo-preview mb-3" id="photoPreview">
                            @else
                                <div class="rounded-circle bg-light border d-inline-flex align-items-center justify-content-center mb-3" style="width:100px;height:100px" id="photoPlaceholder">
                                    <i class="bi bi-person-circle text-muted" style="font-size:48px"></i>
                                </div>
                                <img src="" class="photo-preview mb-3 d-none" id="photoPreview">
                            @endif
                        </div>
                        <div>
                            <label class="btn btn-outline-primary btn-sm" for="foto_profil">
                                <i class="bi bi-camera me-1"></i>Upload Foto
                            </label>
                            <input type="file" name="foto_profil" id="foto_profil" class="d-none" accept="image/*">
                            <div class="text-muted small mt-1">JPG, PNG. Maks 2MB</div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NIP <span class="text-danger">*</span></label>
                            <input type="text" name="nip" value="{{ old('nip', $employee->nip ?? '') }}"
                                class="form-control @error('nip') is-invalid @enderror"
                                placeholder="Nomor Induk Pegawai" required>
                            @error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NIK <span class="text-danger">*</span></label>
                            <input type="text" name="nik" value="{{ old('nik', $employee->nik ?? '') }}"
                                class="form-control @error('nik') is-invalid @enderror"
                                placeholder="Nomor Induk Kependudukan (16 digit)"
                                maxlength="16" required>
                            @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nama Gelar</label>
                            <input type="text" name="nama_gelar" value="{{ old('nama_gelar', $employee->nama_gelar ?? '') }}"
                                class="form-control" placeholder="dr., S.Kep., dll">
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $employee->nama_lengkap ?? '') }}"
                                class="form-control @error('nama_lengkap') is-invalid @enderror"
                                placeholder="Nama lengkap tanpa gelar" required>
                            @error('nama_lengkap')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                                <option value="">Pilih...</option>
                                <option value="L" {{ old('jenis_kelamin', $employee->jenis_kelamin ?? '') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $employee->jenis_kelamin ?? '') === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        
<div class="col-md-4">
    <label class="form-label fw-semibold">Tempat Lahir</label>
    <input type="text" name="tempat_lahir"
        value="{{ old('tempat_lahir') }}"
        class="form-control" placeholder="Kota kelahiran">
</div>

<div class="col-md-4">
    <label class="form-label fw-semibold">Tanggal Lahir</label>
    <input type="date" name="tanggal_lahir"
        value="{{ old('tanggal_lahir') }}"
        class="form-control">
</div>

<div class="col-md-4">
    <label class="form-label fw-semibold">Golongan Darah</label>
    <select name="golongan_darah" class="form-select">
        <option value="">Pilih...</option>
        @foreach(['A','B','AB','O','A+','A-','B+','B-','AB+','AB-','O+','O-'] as $gd)
            <option value="{{ $gd }}" {{ old('golongan_darah') === $gd ? 'selected' : '' }}>{{ $gd }}</option>
        @endforeach
    </select>
</div>

<div class="col-md-6">
    <label class="form-label fw-semibold">Agama</label>
    <select name="agama" class="form-select">
        <option value="">Pilih...</option>
        @foreach(['Islam','Kristen Protestan','Kristen Katolik','Hindu','Buddha','Konghucu'] as $agama)
            <option value="{{ $agama }}" {{ old('agama') === $agama ? 'selected' : '' }}>{{ $agama }}</option>
        @endforeach
    </select>
</div>





<div class="col-md-6">
    <label class="form-label fw-semibold">Status Pernikahan</label>
    <select name="status_pernikahan" class="form-select">
        <option value="">Pilih...</option>
        <option value="belum_menikah" {{ old('status_pernikahan') === 'belum_menikah' ? 'selected' : '' }}>Belum Menikah</option>
        <option value="menikah"       {{ old('status_pernikahan') === 'menikah'       ? 'selected' : '' }}>Menikah</option>
        <option value="cerai_hidup"   {{ old('status_pernikahan') === 'cerai_hidup'   ? 'selected' : '' }}>Cerai Hidup</option>
        <option value="cerai_mati"    {{ old('status_pernikahan') === 'cerai_mati'    ? 'selected' : '' }}>Cerai Mati</option>
    </select>
</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Data Kontak -->
        <div class="tab-pane fade" id="tab-kontak">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-telephone-fill me-2 text-success"></i>Data Kontak</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. HP / WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                <input type="text" name="no_hp" value="{{ old('no_hp', $employee->no_hp ?? '') }}"
                                    class="form-control" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" value="{{ old('email', $employee->email ?? '') }}"
                                    class="form-control" placeholder="email@contoh.com">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Alamat Lengkap</label>
                            <textarea name="alamat" rows="3" class="form-control"
                                placeholder="Jalan, Kelurahan, Kecamatan, Kota, Provinsi">{{ old('alamat', $employee->alamat ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 3: Data Pendidikan -->
        <div class="tab-pane fade" id="tab-pendidikan">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-mortarboard-fill me-2 text-info"></i>Data Pendidikan</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pendidikan Terakhir</label>
                            <select name="pendidikan_terakhir" class="form-select">
                                <option value="">Pilih...</option>
                                @foreach(['SMA/SMK', 'D3', 'D4', 'S1', 'S2', 'S3', 'Profesi'] as $pend)
                                    <option value="{{ $pend }}" {{ old('pendidikan_terakhir', $employee->pendidikan_terakhir ?? '') === $pend ? 'selected' : '' }}>{{ $pend }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Program Studi</label>
                            <input type="text" name="prodi_pendidikan"
                                value="{{ old('prodi_pendidikan') }}"
                                    class="form-control"
                                placeholder="Contoh: Keperawatan, Kedokteran Umum, dll">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tahun Lulus</label>
                            <input type="number" name="tahun_lulus_ijazah"
                                value="{{ old('tahun_lulus_ijazah', $employee->tahun_lulus_ijazah ?? '') }}"
                                class="form-control" placeholder="2020" min="1990" max="{{ date('Y') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor Ijazah</label>
                            <input type="text" name="nomor_ijazah"
                                value="{{ old('nomor_ijazah', $employee->nomor_ijazah ?? '') }}"
                                class="form-control" placeholder="Nomor ijazah">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Dokumen Ijazah</label>
                            @if(isset($employee) && $employee->dokumen_ijazah)
                                <div class="mb-2">
                                    <a href="{{ Storage::url($employee->dokumen_ijazah) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-file-earmark-text me-1"></i>Lihat Ijazah
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="dokumen_ijazah" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">PDF, JPG, PNG. Maks 5MB. {{ isset($employee) && $employee->dokumen_ijazah ? 'Kosongkan jika tidak ingin mengubah.' : '' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 4: Data Kepegawaian -->
        <div class="tab-pane fade" id="tab-kepegawaian">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-briefcase-fill me-2 text-warning"></i>Data Kepegawaian</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jabatan</label>
                            <input type="text" name="jabatan"
                                value="{{ old('jabatan', $employee->jabatan ?? '') }}"
                                class="form-control" placeholder="Dokter, Perawat, dll">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Unit / Departemen</label>
                            <input type="text" name="unit"
                                value="{{ old('unit', $employee->unit ?? '') }}"
                                class="form-control" placeholder="IGD, ICU, Radiologi, dll">
                        </div>

                        <div class="col-md-3">
    <label class="form-label fw-semibold">Golongan Ruang</label>
    <select name="golongan_ruang" class="form-select">
        <option value="">Pilih...</option>
        @foreach(['I/a','I/b','I/c','I/d','II/a','II/b','II/c','II/d','III/a','III/b','III/c','III/d','IV/a','IV/b','IV/c','IV/d','IV/e'] as $gol)
            <option value="{{ $gol }}" {{ old('golongan_ruang') === $gol ? 'selected' : '' }}>{{ $gol }}</option>
        @endforeach
    </select>
</div>

<div class="col-md-3">
    <label class="form-label fw-semibold">TMT PNS</label>
    <input type="date" name="tmt_pns"
        value="{{ old('tmt_pns') }}"
        class="form-control @error('tmt_pns') is-invalid @enderror">
    <div class="form-text">Tanggal Mulai Tugas PNS</div>
    @error('tmt_pns')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

                        <!-- STR -->
                        <div class="col-12"><hr class="my-1"><h6 class="text-muted small fw-semibold">STR - Surat Tanda Registrasi</h6></div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Upload STR</label>
                            @if(isset($employee) && $employee->str_file)
                                <div class="mb-2">
                                    <a href="{{ Storage::url($employee->str_file) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-file-earmark-text me-1"></i>Lihat STR
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="str_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">PDF, JPG, PNG. Maks 5MB.</div>
                        </div>

                        <!-- SIP -->
                        <div class="col-12"><hr class="my-1"><h6 class="text-muted small fw-semibold">SIP - Surat Izin Praktik</h6></div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Upload SIP</label>
                            @if(isset($employee) && $employee->sip_file)
                                <div class="mb-2">
                                    <a href="{{ Storage::url($employee->sip_file) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-file-earmark-text me-1"></i>Lihat SIP
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="sip_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">PDF, JPG, PNG. Maks 5MB.</div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">TMT SIP</label>
                            <input type="date" name="tmt_sip"
                                value="{{ old('tmt_sip', isset($employee) ? $employee->tmt_sip?->format('Y-m-d') : '') }}"
                                class="form-control @error('tmt_sip') is-invalid @enderror">
                            <div class="form-text">Tanggal Mulai Tugas SIP</div>
                            @error('tmt_sip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">TAT SIP</label>
                            <input type="date" name="tat_sip"
                                value="{{ old('tat_sip', isset($employee) ? $employee->tat_sip?->format('Y-m-d') : '') }}"
                                class="form-control @error('tat_sip') is-invalid @enderror">
                            <div class="form-text">Tanggal Akhir Tugas SIP</div>
                            @error('tat_sip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        @if(isset($employee) && $employee->tat_sip)
                        <div class="col-12">
                            @if($employee->sip_status === 'kadaluarsa')
                                <div class="alert alert-danger py-2 small"><i class="bi bi-x-circle me-1"></i>SIP telah kadaluarsa pada {{ $employee->tat_sip->format('d F Y') }}</div>
                            @elseif($employee->sip_status === 'hampir_kadaluarsa')
                                <div class="alert alert-warning py-2 small"><i class="bi bi-exclamation-triangle me-1"></i>SIP akan kadaluarsa dalam <strong>{{ $employee->sip_days_left }} hari</strong> ({{ $employee->tat_sip->format('d F Y') }})</div>
                            @else
                                <div class="alert alert-success py-2 small"><i class="bi bi-check-circle me-1"></i>SIP aktif hingga {{ $employee->tat_sip->format('d F Y') }}</div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-save me-1"></i>{{ isset($employee) ? 'Simpan Perubahan' : 'Tambah Karyawan' }}
        </button>
        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // Photo preview
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

    // Show tab with error
    const errorFields = {
        'nip': 'tab-pribadi', 'nik': 'tab-pribadi', 'nama_lengkap': 'tab-pribadi', 'jenis_kelamin': 'tab-pribadi',
        'no_hp': 'tab-kontak', 'email': 'tab-kontak',
        'jabatan': 'tab-kepegawaian', 'unit': 'tab-kepegawaian', 'tmt_sip': 'tab-kepegawaian', 'tat_sip': 'tab-kepegawaian',
    };

    @if($errors->any())
    const firstError = '{{ $errors->keys()[0] ?? '' }}';
    const targetTab = errorFields[firstError];
    if (targetTab) {
        document.querySelector(`[data-bs-target="#${targetTab}"]`).click();
    }
    @endif
</script>
@endpush
