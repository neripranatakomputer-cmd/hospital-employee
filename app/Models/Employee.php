<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'foto_profil', 'nip', 'nik', 'nama_lengkap', 'nama_gelar', 'jenis_kelamin',
    'tempat_lahir', 'tanggal_lahir', 'status_pernikahan', 'golongan_darah',
    'agama', 'golongan_ruang', 'tmt_pns',                    // tambah ini
    'no_hp', 'email', 'alamat',
    'pendidikan_terakhir', 'prodi_pendidikan',                // tambah prodi_pendidikan
    'nomor_ijazah', 'tahun_lulus_ijazah', 'dokumen_ijazah',
    'jabatan', 'unit', 'str_file', 'sip_file', 'tmt_sip', 'tat_sip',
    'is_active', 'tmt_golongan',
    ];

    protected $casts = [
        'tmt_sip' => 'date',
        'tat_sip'  => 'date',
        'tanggal_lahir'  => 'date',
        'tmt_pns'       => 'date',   // tambah ini
        'tmt_golongan' => 'date',  // tambah ini
        ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function getNamaLengkapGelarAttribute(): string
    {
        return trim($this->nama_gelar . ' ' . $this->nama_lengkap);
    }

    public function getSipStatusAttribute(): string
    {
        if (!$this->tat_sip) return 'tidak_ada';

        $daysLeft = now()->diffInDays($this->tat_sip, false);

        if ($daysLeft < 0) return 'kadaluarsa';
        if ($daysLeft <= 30) return 'hampir_kadaluarsa';
        return 'aktif';
    }

    public function getSipDaysLeftAttribute(): int
    {
        if (!$this->tat_sip) return 0;
        return (int) now()->diffInDays($this->tat_sip, false);
    }

    public function scopeExpiringSip($query, int $days = 30)
    {
        return $query->whereNotNull('tat_sip')
            ->whereDate('tat_sip', '>=', now())
            ->whereDate('tat_sip', '<=', now()->addDays($days));
    }

    public function scopeExpiredSip($query)
    {
        return $query->whereNotNull('tat_sip')
            ->whereDate('tat_sip', '<', now());
    }

    // ── Notifikasi Kenaikan Pangkat (setiap 3 tahun) ──────────────
public function getKenaikanPangkatStatusAttribute(): string
{
    if (!$this->tmt_golongan) return 'tidak_ada';

    $dueDate  = $this->tmt_golongan->copy()->addYears(3);
    $daysLeft = now()->diffInDays($dueDate, false);

    if ($daysLeft < 0)   return 'terlambat';
    if ($daysLeft <= 90) return 'segera';    // 3 bulan sebelum
    return 'belum';
}

public function getKenaikanPangkatDueDateAttribute(): ?\Carbon\Carbon
{
    return $this->tmt_golongan ? $this->tmt_golongan->copy()->addYears(3) : null;
}

public function getKenaikanPangkatDaysLeftAttribute(): int
{
    if (!$this->tmt_golongan) return 0;
    return (int) now()->diffInDays($this->tmt_golongan->copy()->addYears(3), false);
}

// ── Notifikasi Kenaikan Gaji Berkala (setiap 2 tahun) ─────────
public function getKenaikanGajiStatusAttribute(): string
{
    if (!$this->tmt_golongan) return 'tidak_ada';

    $dueDate  = $this->tmt_golongan->copy()->addYears(2);
    $daysLeft = now()->diffInDays($dueDate, false);

    if ($daysLeft < 0)   return 'terlambat';
    if ($daysLeft <= 90) return 'segera';
    return 'belum';
}

public function getKenaikanGajiDueDateAttribute(): ?\Carbon\Carbon
{
    return $this->tmt_golongan ? $this->tmt_golongan->copy()->addYears(2) : null;
}

public function getKenaikanGajiDaysLeftAttribute(): int
{
    if (!$this->tmt_golongan) return 0;
    return (int) now()->diffInDays($this->tmt_golongan->copy()->addYears(2), false);
}

// ── Scopes ────────────────────────────────────────────────────
public function scopeKenaikanPangkatSegera($query, int $days = 90)
{
    return $query->whereNotNull('tmt_golongan')
        ->whereDate('tmt_golongan', '<=', now()->subYears(3)->addDays($days))
        ->whereDate('tmt_golongan', '>', now()->subYears(3));
}

public function scopeKenaikanPangkatTerlambat($query)
{
    return $query->whereNotNull('tmt_golongan')
        ->whereDate('tmt_golongan', '<', now()->subYears(3));
}

public function scopeKenaikanGajiSegera($query, int $days = 90)
{
    return $query->whereNotNull('tmt_golongan')
        ->whereDate('tmt_golongan', '<=', now()->subYears(2)->addDays($days))
        ->whereDate('tmt_golongan', '>', now()->subYears(2));
}

public function scopeKenaikanGajiTerlambat($query)
{
    return $query->whereNotNull('tmt_golongan')
        ->whereDate('tmt_golongan', '<', now()->subYears(2));
}
}
