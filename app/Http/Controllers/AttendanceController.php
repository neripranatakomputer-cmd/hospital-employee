<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('employee')->whereHas('employee');

    if ($request->tanggal) {
        $query->whereDate('tanggal', $request->tanggal);
    } else {
        $query->whereDate('tanggal', today());
    }

    if ($request->employee_id) {
        $query->where('employee_id', $request->employee_id);
    }

    if ($request->status) {
        $query->where('status', $request->status);
    }

    $attendances = $query->latest()->paginate(20)->withQueryString();
    $employees = Employee::where('is_active', true)->orderBy('nama_lengkap')->get();

    return view('attendance.index', compact('attendances', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->orderBy('nama_lengkap')->get();
        return view('attendance.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'tanggal'     => 'required|date',
            'jam_masuk'   => 'nullable|date_format:H:i',
            'jam_keluar'  => 'nullable|date_format:H:i|after:jam_masuk',
            'status'      => 'required|in:hadir,izin,sakit,alpha,cuti',
            'keterangan'  => 'nullable|string',
        ]);

        $validated['sumber'] = 'manual';

        Attendance::updateOrCreate(
            ['employee_id' => $validated['employee_id'], 'tanggal' => $validated['tanggal']],
            $validated
        );

        return redirect()->route('attendance.index')
            ->with('success', 'Absensi berhasil disimpan.');
    }

    public function edit(Attendance $attendance)
    {
        $employees = Employee::where('is_active', true)->orderBy('nama_lengkap')->get();
        return view('attendance.edit', compact('attendance', 'employees'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'jam_masuk'  => 'nullable|date_format:H:i',
            'jam_keluar' => 'nullable|date_format:H:i|after:jam_masuk',
            'status'     => 'required|in:hadir,izin,sakit,alpha,cuti',
            'keterangan' => 'nullable|string',
        ]);

        $attendance->update($validated);

        return redirect()->route('attendance.index')
            ->with('success', 'Absensi berhasil diperbarui.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return back()->with('success', 'Data absensi dihapus.');
    }

    public function syncMachine(Request $request)
{
    try {
        $service = app(\App\Services\AttendanceMachineService::class);
        $date    = $request->get('date', today()->format('Y-m-d'));
        $result  = $service->syncAttendance($date);

        $msg = "Sync berhasil: {$result['synced']} data baru, {$result['skipped']} dilewati.";

        if (!empty($result['errors'])) {
            return back()->with('warning', $msg . ' Beberapa NIP tidak ditemukan.');
        }

        return back()->with('success', $msg);

    } catch (\Exception $e) {
        return back()->with('error', 'Gagal sync: ' . $e->getMessage());
    }
}

public function testMachine()
{
    $service = app(\App\Services\AttendanceMachineService::class);
    $result  = $service->testConnection();
    return response()->json($result);
}
    public function report(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        [$year, $mon] = explode('-', $month);

        $employees = Employee::where('is_active', true)
            ->with(['attendances' => function ($q) use ($year, $mon) {
                $q->whereYear('tanggal', $year)->whereMonth('tanggal', $mon);
            }])
            ->orderBy('nama_lengkap')
            ->get();

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $mon, $year);

        return view('attendance.report', compact('employees', 'month', 'daysInMonth', 'year', 'mon'));
    }

    public function downloadReport(Request $request)
{
    $month = $request->get('month', now()->format('Y-m'));
    [$year, $mon] = explode('-', $month);

    $employees = Employee::where('is_active', true)
        ->with(['attendances' => function ($q) use ($year, $mon) {
            $q->whereYear('tanggal', $year)->whereMonth('tanggal', $mon);
        }])
        ->orderBy('nama_lengkap')
        ->get();

    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $mon, $year);
    $monthLabel  = \Carbon\Carbon::create($year, $mon)->translatedFormat('F Y');

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Absensi {$monthLabel}");

    // ── STYLE HELPER ──────────────────────────────────────────
    $styleBold       = ['font' => ['bold' => true]];
    $styleCenter     = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];
    $styleBoldCenter = ['font' => ['bold' => true], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];
    $styleBorder     = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]];

    $headerFill = fn(string $color) => [
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $color]],
        'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    ];

    // ── JUDUL ─────────────────────────────────────────────────
    $totalCols = 4 + $daysInMonth + 5; // No+Nama+Jabatan+Unit + hari + H+I+S+A+C
    $lastCol   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalCols);

    $sheet->mergeCells("A1:{$lastCol}1");
    $sheet->setCellValue('A1', "REKAP ABSENSI KARYAWAN - {$monthLabel}");
    $sheet->getStyle('A1')->applyFromArray([
        'font'      => ['bold' => true, 'size' => 14],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    ]);
    $sheet->getRowDimension(1)->setRowHeight(25);

    $sheet->mergeCells("A2:{$lastCol}2");
    $sheet->setCellValue('A2', "Periode: {$monthLabel}");
    $sheet->getStyle('A2')->applyFromArray($styleCenter);

    // ── HEADER TABEL ──────────────────────────────────────────
    $row = 4;
    $sheet->setCellValue('A' . $row, 'No');
    $sheet->setCellValue('B' . $row, 'Nama Lengkap');
    $sheet->setCellValue('C' . $row, 'Jabatan');
    $sheet->setCellValue('D' . $row, 'Unit');

    $sheet->getStyle("A{$row}:D{$row}")->applyFromArray($headerFill('FF4472C4'));
    $sheet->getColumnDimension('A')->setWidth(5);
    $sheet->getColumnDimension('B')->setWidth(28);
    $sheet->getColumnDimension('C')->setWidth(22);
    $sheet->getColumnDimension('D')->setWidth(18);

    // Header hari (1-31)
    for ($d = 1; $d <= $daysInMonth; $d++) {
        $col     = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($d + 4);
        $dayName = \Carbon\Carbon::create($year, $mon, $d)->format('D');
        $isWeekend = in_array(\Carbon\Carbon::create($year, $mon, $d)->dayOfWeek, [0, 6]);

        $sheet->setCellValue($col . $row, $d);
        $sheet->getComment($col . $row)->getText()->createTextRun($dayName);
        $sheet->getColumnDimension($col)->setWidth(4);
        $sheet->getStyle($col . $row)->applyFromArray(
            $headerFill($isWeekend ? 'FF7F7F7F' : 'FF4472C4')
        );
    }

    // Header rekap H/I/S/A/C
    $rekapCols = ['H' => 'FF70AD47', 'I' => 'FF00B0F0', 'S' => 'FFFFC000', 'A' => 'FFFF0000', 'C' => 'FF7030A0'];
    $rekapStart = $daysInMonth + 5;
    $ri = 0;
    foreach ($rekapCols as $label => $color) {
        $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($rekapStart + $ri);
        $sheet->setCellValue($col . $row, $label);
        $sheet->getColumnDimension($col)->setWidth(5);
        $sheet->getStyle($col . $row)->applyFromArray($headerFill($color));
        $ri++;
    }

    // ── DATA ROWS ─────────────────────────────────────────────
    $dataRow = $row + 1;
    foreach ($employees as $i => $emp) {
        $attByDate = $emp->attendances->keyBy(fn($a) => (int) $a->tanggal->format('j'));

        $sheet->setCellValue('A' . $dataRow, $i + 1);
        $sheet->setCellValue('B' . $dataRow, ($emp->nama_gelar ? $emp->nama_gelar . ' ' : '') . $emp->nama_lengkap);
        $sheet->setCellValue('C' . $dataRow, $emp->jabatan ?? '-');
        $sheet->setCellValue('D' . $dataRow, $emp->unit ?? '-');

        $countH = $countI = $countS = $countA = $countC = 0;

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $col      = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($d + 4);
            $att      = $attByDate[$d] ?? null;
            $isWeekend = in_array(\Carbon\Carbon::create($year, $mon, $d)->dayOfWeek, [0, 6]);

            if ($att) {
                $symbols = ['hadir' => '✓', 'izin' => 'I', 'sakit' => 'S', 'alpha' => 'A', 'cuti' => 'C'];
                $colors  = ['hadir' => 'FFE2EFDA', 'izin' => 'FFDAE3F3', 'sakit' => 'FFFFF2CC', 'alpha' => 'FFFCE4D6', 'cuti' => 'FFEDE7F6'];
                $sheet->setCellValue($col . $dataRow, $symbols[$att->status] ?? '?');
                $sheet->getStyle($col . $dataRow)->applyFromArray([
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $colors[$att->status] ?? 'FFFFFFFF']],
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                ]);
                match($att->status) {
                    'hadir' => $countH++, 'izin' => $countI++,
                    'sakit' => $countS++, 'alpha' => $countA++,
                    'cuti'  => $countC++, default => null,
                };
            } elseif ($isWeekend) {
                $sheet->getStyle($col . $dataRow)->applyFromArray([
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9D9D9']],
                ]);
            }
        }

        // Isi rekap
        $rekapValues = [$countH, $countI, $countS, $countA, $countC];
        foreach ($rekapValues as $ri => $val) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($rekapStart + $ri);
            $sheet->setCellValue($col . $dataRow, $val);
            $sheet->getStyle($col . $dataRow)->applyFromArray($styleBoldCenter);
        }

        // Zebra stripe
        if ($i % 2 === 0) {
            $sheet->getStyle("A{$dataRow}:D{$dataRow}")->applyFromArray([
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF2F2F2']],
            ]);
        }

        $sheet->getStyle("A{$dataRow}:B{$dataRow}")->applyFromArray($styleBold);
        $dataRow++;
    }

    // ── BORDER SEMUA TABEL ────────────────────────────────────
    $sheet->getStyle("A{$row}:{$lastCol}" . ($dataRow - 1))->applyFromArray($styleBorder);

    // ── KETERANGAN ────────────────────────────────────────────
    $dataRow++;
    $sheet->setCellValue('A' . $dataRow, 'Keterangan:');
    $sheet->getStyle('A' . $dataRow)->applyFromArray($styleBold);
    $dataRow++;
    foreach (['✓ = Hadir', 'I = Izin', 'S = Sakit', 'A = Alpha', 'C = Cuti', '░ = Hari Libur'] as $ket) {
        $sheet->setCellValue('A' . $dataRow, $ket);
        $dataRow++;
    }

    // ── DOWNLOAD ──────────────────────────────────────────────
    $filename = "rekap-absensi-{$month}.xlsx";
    $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

    $tempFile = tempnam(sys_get_temp_dir(), 'attendance_');
    $writer->save($tempFile);

    return response()->download($tempFile, $filename, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ])->deleteFileAfterSend(true);
}
}
