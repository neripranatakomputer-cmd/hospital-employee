<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class CreateEmployeeTemplate extends Command
{
    protected $signature = 'template:employee';
    protected $description = 'Buat template Excel import karyawan';

    public function handle()
    {
        $spreadsheet = new Spreadsheet();

        // ── Sheet 1: Template Data ─────────────────────────────
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Karyawan');

        $headers = [
            'nip'                => 'NIP',
            'nik'                => 'NIK',
            'nama_lengkap'       => 'Nama Lengkap',
            'nama_gelar'         => 'Nama Gelar',
            'jenis_kelamin'      => 'Jenis Kelamin (L/P)',
            'tempat_lahir'       => 'Tempat Lahir',
            'tanggal_lahir'      => 'Tanggal Lahir (YYYY-MM-DD)',
            'status_pernikahan'  => 'Status Pernikahan',
            'golongan_darah'     => 'Golongan Darah',
            'no_hp'              => 'No. HP',
            'email'              => 'Email',
            'alamat'             => 'Alamat',
            'pendidikan_terakhir'=> 'Pendidikan Terakhir',
            'nomor_ijazah'       => 'Nomor Ijazah',
            'tahun_lulus_ijazah' => 'Tahun Lulus',
            'jabatan'            => 'Jabatan',
            'unit'               => 'Unit',
            'tmt_sip'            => 'TMT SIP (YYYY-MM-DD)',
            'tat_sip'            => 'TAT SIP (YYYY-MM-DD)',
        ];

        // Tulis header baris 1 (nama kolom untuk sistem/key)
        // Tulis header baris 2 (label yang mudah dibaca)
        $col = 1;
        foreach ($headers as $key => $label) {
            $colLetter = Coordinate::stringFromColumnIndex($col);

            // Baris 1: key (untuk sistem)
            $sheet->setCellValue($colLetter . '1', $key);
            $sheet->getStyle($colLetter . '1')->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F3864']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // Baris 2: label (untuk pengguna)
            $sheet->setCellValue($colLetter . '2', $label);
            $sheet->getStyle($colLetter . '2')->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['argb' => 'FF000000'], 'size' => 10],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9E1F2']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // Set lebar kolom
            $sheet->getColumnDimension($colLetter)->setWidth(22);
            $col++;
        }

        // Baris 3: contoh data
        $example = [
            '19850101001',          // nip
            '3201010185001001',     // nik
            'Budi Santoso',         // nama_lengkap
            'dr.Budi Santoso',      // nama_gelar
            'L',                    // jenis_kelamin
            'Bukittinggi',          // tempat_lahir
            '1985-01-01',           // tanggal_lahir
            'menikah',              // status_pernikahan (belum_menikah/menikah/cerai_hidup/cerai_mati)
            'O+',                   // golongan_darah
            '08123456789',          // no_hp
            'budi@rs.com',          // email
            'Jl. Contoh No. 1',    // alamat
            'S1',                   // pendidikan_terakhir
            'IJZ-001/2020',         // nomor_ijazah
            '2020',                 // tahun_lulus_ijazah
            'Dokter Umum',          // jabatan
            'Poli Umum',            // unit
            '2024-01-01',           // tmt_sip
            '2027-01-01',           // tat_sip
        ];

        $col = 1;
        foreach ($example as $value) {
            $colLetter = Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValueExplicit(
                $colLetter . '3',
                $value,
                \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING // Force string agar NIP/NIK tidak jadi scientific notation
            );
            $sheet->getStyle($colLetter . '3')->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEDEDED']],
            ]);
            $col++;
        }

        // Border semua data
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle("A1:{$lastCol}3")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Freeze baris header
        $sheet->freezePane('A3');

        // Tinggi baris
        $sheet->getRowDimension(1)->setRowHeight(20);
        $sheet->getRowDimension(2)->setRowHeight(30);
        $sheet->getRowDimension(3)->setRowHeight(20);

        // ── Sheet 2: Keterangan ───────────────────────────────
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Keterangan');

        $notes = [
            ['Field', 'Keterangan', 'Contoh / Pilihan'],
            ['nip', 'Nomor Induk Pegawai', '19850101001'],
            ['nik', 'NIK KTP 16 digit', '3201010185001001'],
            ['nama_lengkap', 'Nama tanpa gelar', 'Budi Santoso'],
            ['nama_gelar', 'Gelar akademik', 'dr. / S.Kep. / S.Kom'],
            ['jenis_kelamin', 'Huruf kapital', 'L atau P'],
            ['tempat_lahir', 'Kota kelahiran', 'Bukittinggi'],
            ['tanggal_lahir', 'Format YYYY-MM-DD', '1985-01-01'],
            ['status_pernikahan', 'Pilih salah satu', 'belum_menikah / menikah / cerai_hidup / cerai_mati'],
            ['golongan_darah', 'Pilih salah satu', 'A / B / AB / O / A+ / A- / B+ / B- / AB+ / AB- / O+ / O-'],
            ['no_hp', 'Nomor WhatsApp aktif', '08123456789'],
            ['email', 'Email aktif', 'nama@email.com'],
            ['alamat', 'Alamat lengkap', 'Jl. Nama No. 1, Kota'],
            ['pendidikan_terakhir', 'Pilih salah satu', 'SMA/SMK / D3 / D4 / S1 / S2 / S3 / Profesi'],
            ['nomor_ijazah', 'Nomor ijazah terakhir', 'IJZ-001/2020'],
            ['tahun_lulus_ijazah', '4 digit tahun', '2020'],
            ['jabatan', 'Jabatan di RS', 'Dokter Umum / Perawat'],
            ['unit', 'Unit/Departemen', 'IGD / ICU / Poli Umum'],
            ['tmt_sip', 'Tanggal Mulai Tugas SIP', '2024-01-01'],
            ['tat_sip', 'Tanggal Akhir Tugas SIP', '2027-01-01'],
        ];

        foreach ($notes as $r => $row) {
            foreach ($row as $c => $val) {
                $colLetter = Coordinate::stringFromColumnIndex($c + 1);
                $sheet2->setCellValue($colLetter . ($r + 1), $val);

                if ($r === 0) {
                    $sheet2->getStyle($colLetter . '1')->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F3864']],
                    ]);
                }
            }
        }

        $sheet2->getColumnDimension('A')->setWidth(22);
        $sheet2->getColumnDimension('B')->setWidth(30);
        $sheet2->getColumnDimension('C')->setWidth(55);
        $sheet2->getStyle('A1:C' . count($notes))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Set sheet aktif ke sheet 1
        $spreadsheet->setActiveSheetIndex(0);

        // ── Simpan file ───────────────────────────────────────
        if (!is_dir(public_path('templates'))) {
            mkdir(public_path('templates'), 0755, true);
        }

        $path = public_path('templates/employee_template.xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        $this->info("✅ Template berhasil dibuat: {$path}");
        $this->info("📋 Total kolom: " . count($headers));
        $this->line("   Buka public/templates/employee_template.xlsx");
    }
}