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
        'no_hp', 'email', 'alamat',
        'pendidikan_terakhir', 'nomor_ijazah', 'tahun_lulus_ijazah', 'dokumen_ijazah',
        'jabatan', 'unit', 'str_file', 'sip_file', 'tmt_sip', 'tat_sip',
        'is_active',
    ];

    protected $casts = [
        'tmt_sip' => 'date',
        'tat_sip'  => 'date',
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
}
