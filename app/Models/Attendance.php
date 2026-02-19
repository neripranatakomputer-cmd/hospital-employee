<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id', 'tanggal', 'jam_masuk', 'jam_keluar', 'status', 'keterangan', 'sumber',
    ];

    protected $casts = [
        'tanggal' => 'date',
        // HAPUS jam_masuk dan jam_keluar dari casts jika ada
    ];

    protected function jamMasuk(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn ($value) => $value ? substr($value, 0, 5) : null,
        );
    }

    protected function jamKeluar(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn ($value) => $value ? substr($value, 0, 5) : null,
        );
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}