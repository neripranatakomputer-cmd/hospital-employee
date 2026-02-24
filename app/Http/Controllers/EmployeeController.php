<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Imports\EmployeeImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::query();

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama_lengkap', 'like', "%{$request->search}%")
                  ->orWhere('nip', 'like', "%{$request->search}%")
                  ->orWhere('nik', 'like', "%{$request->search}%")
                  ->orWhere('jabatan', 'like', "%{$request->search}%")
                  ->orWhere('unit', 'like', "%{$request->search}%");
            });
        }

        if ($request->unit) {
            $query->where('unit', $request->unit);
        }

        if ($request->jabatan) {
            $query->where('jabatan', $request->jabatan);
        }

        $employees = $query->latest()->paginate(15)->withQueryString();

        $units = Employee::distinct()->pluck('unit')->filter()->sort()->values();
        $jabatans = Employee::distinct()->pluck('jabatan')->filter()->sort()->values();

        // SIP expiry alerts
        $sipExpiringCount = Employee::expiringSip(30)->count();
        $sipExpiredCount = Employee::expiredSip()->count();

        return view('employees.index', compact(
            'employees', 'units', 'jabatans', 'sipExpiringCount', 'sipExpiredCount'
        ));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateEmployee($request);

        // Handle file uploads
        $validated = array_merge($validated, $this->handleFileUploads($request));

        Employee::create($validated);

        return redirect()->route('employees.index')
            ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function show(Employee $employee)
    {
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $this->validateEmployee($request, $employee->id);

        // Handle file uploads
        $fileData = $this->handleFileUploads($request, $employee);
        $validated = array_merge($validated, $fileData);

        $employee->update($validated);

        return redirect()->route('employees.index')
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')
            ->with('success', 'Karyawan berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $import = new EmployeeImport();
            Excel::import($import, $request->file('file'));

            $successCount = $import->getSuccessCount();
            $errors = $import->getErrors();

            if (count($errors) > 0) {
                $msg = "Import selesai: {$successCount} berhasil, " . count($errors) . " gagal.";
                return back()->with('warning', $msg)->with('import_errors', $errors);
            }

            return back()->with('success', "Import berhasil: {$successCount} karyawan ditambahkan.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return response()->download(public_path('templates/employee_template.xlsx'));
    }

    public function sipAlerts()
    {
        $expiring = Employee::expiringSip(30)->get();
        $expired = Employee::expiredSip()->get();

        return view('employees.sip-alerts', compact('expiring', 'expired'));
    }

    private function validateEmployee(Request $request, $ignoreId = null): array
    {
        return $request->validate([
            // Data Pribadi
            'foto_profil'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'nip'                 => 'required|string|unique:employees,nip' . ($ignoreId ? ",{$ignoreId}" : ''),
            'nik'                 => 'required|string|size:16|unique:employees,nik' . ($ignoreId ? ",{$ignoreId}" : ''),
            'nama_lengkap'        => 'required|string|max:255',
            'nama_gelar'          => 'nullable|string|max:100',
            'jenis_kelamin'       => 'required|in:L,P',
            'tempat_lahir'      => 'nullable|string|max:100',      // tambah
            'tanggal_lahir'     => 'nullable|date',                // tambah
            'status_pernikahan' => 'nullable|in:belum_menikah,menikah,cerai_hidup,cerai_mati', // tambah
            'golongan_darah'    => 'nullable|in:A,B,AB,O,A+,A-,B+,B-,AB+,AB-,O+,O-',         // tambah
            // Data Kontak
            'no_hp'               => 'nullable|string|max:20',
            'email'               => 'nullable|email|max:255',
            'alamat'              => 'nullable|string',
            // Data Pendidikan
            'pendidikan_terakhir' => 'nullable|string|max:100',
            'nomor_ijazah'        => 'nullable|string|max:100',
            'tahun_lulus_ijazah'  => 'nullable|digits:4|integer',
            'dokumen_ijazah'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            // Data Kepegawaian
            'jabatan'             => 'nullable|string|max:100',
            'unit'                => 'nullable|string|max:100',
            'str_file'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'sip_file'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'tmt_sip'             => 'nullable|date',
            'tat_sip'             => 'nullable|date|after_or_equal:tmt_sip',
            'tmt_golongan'        => 'nullable|date',

            // Data Pribadi - tambahkan setelah golongan_darah
            'agama'               => 'nullable|string|max:50',
            'golongan_ruang'      => 'nullable|string|max:20',
            'tmt_pns'             => 'nullable|date',

// Data Pendidikan - tambahkan setelah pendidikan_terakhir
'prodi_pendidikan' => 'nullable|string|max:150',
        ]);
    }

    private function handleFileUploads(Request $request, ?Employee $employee = null): array
    {
        $data = [];

        $fileFields = [
            'foto_profil'    => 'profiles',
            'dokumen_ijazah' => 'ijazah',
            'str_file'       => 'str',
            'sip_file'       => 'sip',
        ];

        foreach ($fileFields as $field => $folder) {
            if ($request->hasFile($field)) {
                // Delete old file
                if ($employee && $employee->$field) {
                    Storage::disk('public')->delete($employee->$field);
                }
                $data[$field] = $request->file($field)->store("employees/{$folder}", 'public');
            }
        }

        return $data;
    }
}
