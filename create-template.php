#!/usr/bin/env php
<?php
/**
 * Run: php create-template.php
 * This creates the Excel template for employee import
 *
 * Or use artisan:
 * php artisan tinker
 * >>> \Maatwebsite\Excel\Facades\Excel::download(new App\Exports\EmployeeTemplateExport, 'template.xlsx')
 */

// The template should have these headers in row 1:
$headers = [
    'nip',
    'nik',
    'nama_lengkap',
    'nama_gelar',
    'jenis_kelamin',   // L or P
    'no_hp',
    'email',
    'alamat',
    'pendidikan_terakhir', // SMA/SMK, D3, D4, S1, S2, S3, Profesi
    'nomor_ijazah',
    'tahun_lulus_ijazah',
    'jabatan',
    'unit',
    'tmt_sip',         // YYYY-MM-DD
    'tat_sip',         // YYYY-MM-DD
];

// Example row:
$example = [
    '19850101001',     // nip
    '3201010185001001', // nik (16 digits)
    'Budi Santoso',    // nama_lengkap
    'dr.',             // nama_gelar
    'L',               // jenis_kelamin
    '08123456789',     // no_hp
    'budi@rs.com',     // email
    'Jl. Contoh No. 1, Kota', // alamat
    'S1',             // pendidikan_terakhir
    'IJZ-001/2020',   // nomor_ijazah
    '2020',           // tahun_lulus_ijazah
    'Dokter Umum',    // jabatan
    'Poli Umum',      // unit
    '2024-01-01',     // tmt_sip
    '2027-01-01',     // tat_sip
];

echo "Template headers:\n";
echo implode("\t", $headers) . "\n";
echo "\nExample row:\n";
echo implode("\t", $example) . "\n";
