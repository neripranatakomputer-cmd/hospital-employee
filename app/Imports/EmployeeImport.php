<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class EmployeeImport implements ToCollection, WithHeadingRow
{
    private int $successCount = 0;
    private array $importErrors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2;

            try {
                $nip  = $this->formatNumber($row['nip'] ?? '');
                $nik  = $this->formatNumber($row['nik'] ?? '');
                $nama = trim((string) ($row['nama_lengkap'] ?? ''));

                // Skip baris kosong
                if (empty($nip) || empty($nama)) {
                    continue;
                }

                // Cek duplikat NIP termasuk soft deleted
                if (Employee::withTrashed()->where('nip', $nip)->exists()) {
                    $this->importErrors[] = "Baris {$rowNum}: NIP {$nip} sudah ada, dilewati.";
                    continue;
                }

                // Cek duplikat NIK termasuk soft deleted
                if ($nik && Employee::withTrashed()->where('nik', $nik)->exists()) {
                    $this->importErrors[] = "Baris {$rowNum}: NIK {$nik} sudah ada, dilewati.";
                    continue;
                }

                $jk = strtoupper(trim((string) ($row['jenis_kelamin'] ?? 'L')));
                if (!in_array($jk, ['L', 'P'])) $jk = 'L';

                Employee::create([
                    'nip'                 => $nip,
                    'nik'                 => $nik ?: null,
                    'nama_lengkap'        => $nama,
                    'nama_gelar'          => trim((string) ($row['nama_gelar'] ?? '')) ?: null,
                    'jenis_kelamin'       => $jk,
                    'tempat_lahir'      => trim((string) ($row['tempat_lahir'] ?? '')) ?: null,
    'tanggal_lahir'     => $this->parseDate($row['tanggal_lahir'] ?? null),
    'status_pernikahan' => trim((string) ($row['status_pernikahan'] ?? '')) ?: null,
    'golongan_darah'    => trim((string) ($row['golongan_darah'] ?? '')) ?: null,
                    'no_hp'               => $this->formatNumber($row['no_hp'] ?? '') ?: null,
                    'email'               => trim((string) ($row['email'] ?? '')) ?: null,
                    'alamat'              => trim((string) ($row['alamat'] ?? '')) ?: null,
                    'pendidikan_terakhir' => trim((string) ($row['pendidikan_terakhir'] ?? '')) ?: null,
                    'nomor_ijazah'        => trim((string) ($row['nomor_ijazah'] ?? '')) ?: null,
                    'tahun_lulus_ijazah'  => !empty($row['tahun_lulus_ijazah']) ? (int) $row['tahun_lulus_ijazah'] : null,
                    'jabatan'             => trim((string) ($row['jabatan'] ?? '')) ?: null,
                    'unit'                => trim((string) ($row['unit'] ?? '')) ?: null,
                    'tmt_sip'             => $this->parseDate($row['tmt_sip'] ?? null),
                    'tat_sip'             => $this->parseDate($row['tat_sip'] ?? null),
                ]);

                $this->successCount++;

            } catch (\Exception $e) {
                $this->importErrors[] = "Baris {$rowNum}: " . $e->getMessage();
            }
        }
    }

    private function formatNumber($value): string
    {
        if ($value === null || $value === '') return '';

        // Jika string biasa dan bukan numeric, return langsung
        if (is_string($value) && !is_numeric($value)) {
            return trim($value);
        }

        // Handle scientific notation: 1.9900412202506E+17 -> "199004122025060000"
        if (is_float($value) || (is_string($value) && stripos($value, 'e') !== false)) {
            return sprintf('%.0f', (float) $value);
        }

        return trim((string) $value);
    }

    private function parseDate($value): ?string
    {
        if (!$value || trim((string) $value) === '') return null;
        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                    ->format('Y-m-d');
            }
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getErrors(): array
    {
        return $this->importErrors;
    }
}