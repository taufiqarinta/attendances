<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Payroll\PayrollPeriod;
use App\Models\Payroll\PayrollPeriodEmployee;
use App\Models\Payroll\PayrollSlip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayrollController extends Controller
{
    private $monthNames = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function index(Request $request)
    {
        $periods = PayrollPeriod::orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->get();

        if (!$request->has('period_id') && $periods->isNotEmpty()) {
            $request->merge(['period_id' => $periods->first()->id]);
        }

        if (!$request->has('plant')) {
            $request->merge(['plant' => '1000']);
        }

        $slipColumns = $this->slipListColumns();

        $query = PayrollPeriodEmployee::with(['period', 'slip' => function ($q) use ($slipColumns) {
            $q->where('status', 'ACTIVE')->select($slipColumns);
        }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        if ($request->filled('period_id')) {
            $query->where('payroll_period_id', $request->period_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'Uploaded') {
                $query->whereHas('slip', function ($q) {
                    $q->where('status', 'ACTIVE');
                });
            } elseif ($request->status === 'Belum Upload') {
                $query->whereDoesntHave('slip', function ($q) {
                    $q->where('status', 'ACTIVE');
                });
            }
        }

        if ($request->filled('plant')) {
            $query->where('plant', $request->plant);
        }

        if ($request->filled('department')) {
            $query->where('dept', $request->department);
        }

        $perPage = $request->input('per_page', 10);
        $employees = $query->orderBy('nama', 'asc')
            ->orderBy('nik', 'asc')
            ->paginate($perPage)
            ->appends($request->all());

        $totalAll = (clone $query)->count();
        $totalUploaded = (clone $query)->whereHas('slip', function ($q) {
            $q->where('status', 'ACTIVE');
        })->count();
        $totalNotUploaded = $totalAll - $totalUploaded;

        $departments = PayrollPeriodEmployee::select('dept')
            ->distinct()
            ->whereNotNull('dept')
            ->where('dept', '!=', '')
            ->orderBy('dept')
            ->pluck('dept');

        $filters = $request->only(['search', 'period_id', 'status', 'plant', 'department', 'per_page']);

        $hasActiveFilter = $request->filled('search')
            || $request->filled('period_id')
            || $request->filled('status')
            || $request->filled('plant')
            || $request->filled('department');

        $filterInfoParts = [];
        if ($request->filled('search')) {
            $filterInfoParts[] = 'Pencarian: "' . $request->search . '"';
        }
        if ($request->filled('period_id')) {
            $activePeriod = $periods->firstWhere('id', (int) $request->period_id);
            if ($activePeriod) {
                $filterInfoParts[] = 'Periode: ' . $activePeriod->period_name;
            }
        }
        if ($request->filled('status')) {
            $filterInfoParts[] = 'Status: ' . $request->status;
        }
        if ($request->filled('plant')) {
            $plantLabels = [
                '1000' => '1000 - HO - Manager & Director',
                '1001' => '1001 - KOBIN',
                '1002' => '1002 - CAKK',
                '1003' => '1003 - PK-2',
                '1004' => '1004 - MISS',
            ];
            $filterInfoParts[] = 'Plant: ' . ($plantLabels[$request->plant] ?? $request->plant);
        }
        if ($request->filled('department')) {
            $filterInfoParts[] = 'Departemen: ' . $request->department;
        }
        $filterInfo = implode(' • ', $filterInfoParts);

        return view('payslip.payslip-management.index', compact(
            'employees', 'periods', 'departments',
            'totalAll', 'totalUploaded', 'totalNotUploaded', 'filters',
            'hasActiveFilter', 'filterInfo'
        ));
    }

    public function generatePage()
    {
        return view('payslip.payslip-management.generate');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'period' => 'required|string',
            'employees' => 'required|array|min:1',
            'employees.*.nik' => 'required|string',
            'employees.*.nama' => 'required|string',
        ]);

        $periodParts = explode('-', $request->period);
        if (count($periodParts) !== 2) {
            return response()->json([
                'success' => false,
                'message' => 'Format periode tidak valid.',
            ], 422);
        }

        $periodYear = (int) $periodParts[0];
        $periodMonth = (int) $periodParts[1];

        $monthName = $this->monthNames[$periodMonth] ?? '';
        if (!$monthName) {
            return response()->json([
                'success' => false,
                'message' => 'Bulan tidak valid.',
            ], 422);
        }

        $periodCode = "{$monthName} {$periodYear}";

        $existingPeriod = PayrollPeriod::where('period_year', $periodYear)
            ->where('period_month', $periodMonth)
            ->first();

        if ($existingPeriod) {
            $submittedNiks = collect($request->employees)->pluck('nik')->toArray();

            $duplicateEmployees = PayrollPeriodEmployee::where('payroll_period_id', $existingPeriod->id)
                ->whereIn('nik', $submittedNiks)
                ->pluck('nama', 'nik')
                ->toArray();

            if (!empty($duplicateEmployees)) {
                $names = array_values($duplicateEmployees);
                return response()->json([
                    'success' => false,
                    'message' => 'Beberapa karyawan sudah terdaftar di periode ' . $periodCode . '.',
                    'duplicate_names' => $names,
                ], 422);
            }

            try {
                DB::connection('db_payslip')->beginTransaction();

                PayrollPeriodEmployee::insert(
                    $this->buildEmployeeRows($existingPeriod->id, $request->employees)
                );

                $existingPeriod->total_employees = PayrollPeriodEmployee::where('payroll_period_id', $existingPeriod->id)->count();
                $existingPeriod->save();

                DB::connection('db_payslip')->commit();

                return response()->json([
                    'success' => true,
                    'message' => count($request->employees) . ' karyawan berhasil ditambahkan ke periode ' . $periodCode . '.',
                ]);

            } catch (\Exception $e) {
                DB::connection('db_payslip')->rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage(),
                ], 500);
            }
        }

        try {
            DB::connection('db_payslip')->beginTransaction();

            $period = PayrollPeriod::create([
                'period_code' => $periodCode,
                'period_name' => $periodCode,
                'period_year' => $periodYear,
                'period_month' => $periodMonth,
                'status' => 'COMPLETED',
                'total_employees' => count($request->employees),
                'total_uploaded' => 0,
                'created_by' => session('username'),
            ]);

            PayrollPeriodEmployee::insert(
                $this->buildEmployeeRows($period->id, $request->employees)
            );

            DB::connection('db_payslip')->commit();

            return response()->json([
                'success' => true,
                'message' => 'Periode ' . $periodCode . ' berhasil digenerate untuk ' . count($request->employees) . ' karyawan.',
            ]);

        } catch (\Exception $e) {
            DB::connection('db_payslip')->rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function slipListColumns()
    {
        return [
            'id',
            'payroll_period_id',
            'payroll_period_employee_id',
            'file_name',
            'original_file_size',
            'encrypted_file_size',
            'status',
            'uploaded_by',
            'uploaded_at',
            'created_at',
        ];
    }

    private function deriveSlipKey(string $password, string $salt, string $version): string
    {
        if ($version === 'v1') {
            if (!function_exists('sodium_crypto_pwhash')) {
                throw new \RuntimeException('Argon2id (sodium) tidak tersedia di server.');
            }

            return sodium_crypto_pwhash(
                32,
                $password,
                $salt,
                SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
                SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
            );
        }

        throw new \RuntimeException('Versi enkripsi tidak didukung: ' . $version);
    }

    private function buildEmployeeRows($periodId, array $employees)
    {
        $rows = [];
        $now = now();
        foreach ($employees as $emp) {
            $rows[] = [
                'payroll_period_id' => $periodId,
                'nik' => $emp['nik'],
                'nama' => $emp['nama'],
                'email' => $emp['email'] ?? null,
                'level' => $emp['level'] ?? null,
                'plant' => $emp['plant'] ?? null,
                'comp' => $emp['comp'] ?? null,
                'tglmasuk' => $emp['tglmasuk'] ?? null,
                'divisi' => $emp['divisi'] ?? null,
                'dept' => $emp['dept'] ?? null,
                'jabatan' => $emp['jabatan'] ?? null,
                'kode_jabatan' => $emp['kode_jabatan'] ?? null,
                'role' => $emp['role'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        return $rows;
    }

    public function getEmployees(Request $request)
    {
        $slipColumns = $this->slipListColumns();

        $query = PayrollPeriodEmployee::with(['period', 'slip' => function ($q) use ($slipColumns) {
            $q->where('status', 'ACTIVE')->select($slipColumns);
        }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        if ($request->filled('period_id')) {
            $query->where('payroll_period_id', $request->period_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'Uploaded') {
                $query->whereHas('slip', function ($q) {
                    $q->where('status', 'ACTIVE');
                });
            } elseif ($request->status === 'Belum Upload') {
                $query->whereDoesntHave('slip', function ($q) {
                    $q->where('status', 'ACTIVE');
                });
            }
        }

        if ($request->filled('plant')) {
            $query->where('plant', $request->plant);
        }

        if ($request->filled('department')) {
            $query->where('dept', $request->department);
        }

        $query->orderBy('created_at', 'desc');

        $employees = $query->get();

        $employees->each(function ($emp) {
            $emp->period_name = $emp->period ? $emp->period->period_name : '';
            $emp->period_code = $emp->period ? $emp->period->period_code : '';
            $emp->has_slip = $emp->slip ? true : false;
            $emp->slip_status = $emp->slip ? 'Uploaded' : 'Belum Upload';
            $emp->uploaded_by = $emp->slip ? $emp->slip->uploaded_by : '-';
            $emp->uploaded_at = $emp->slip ? ($emp->slip->uploaded_at ? $emp->slip->uploaded_at->format('d M Y H:i') : '-') : '-';
        });

        $totalAll = $employees->count();
        $uploaded = $employees->where('has_slip', true)->count();
        $notUploaded = $employees->where('has_slip', false)->count();

        $departments = PayrollPeriodEmployee::select('dept')
            ->distinct()
            ->whereNotNull('dept')
            ->where('dept', '!=', '')
            ->orderBy('dept')
            ->pluck('dept');

        return response()->json([
            'success' => true,
            'data' => $employees->values(),
            'summary' => [
                'total' => $totalAll,
                'uploaded' => $uploaded,
                'not_uploaded' => $notUploaded,
            ],
            'departments' => $departments,
        ]);
    }

    public function uploadSlip(Request $request)
    {
        $request->validate([
            'period_id' => 'required|integer',
            'employee_id' => 'required|integer',
            'encryption_code' => 'required|string|min:6',
            'file' => 'required|file|mimes:pdf|max:5120',
        ]);

        $period = PayrollPeriod::find($request->period_id);
        if (!$period) {
            return response()->json([
                'success' => false,
                'message' => 'Periode tidak ditemukan.',
            ], 404);
        }

        $employee = PayrollPeriodEmployee::where('id', $request->employee_id)
            ->where('payroll_period_id', $period->id)
            ->first();
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak terdaftar pada periode ' . $period->period_name . '.',
            ], 422);
        }

        $existingSlip = PayrollSlip::where('payroll_period_employee_id', $employee->id)->first();
        if ($existingSlip) {
            return response()->json([
                'success' => false,
                'message' => 'Slip gaji ' . $employee->nama . ' sudah diupload pada periode ' . $period->period_name . '.',
            ], 422);
        }

        try {
            $pdfContent = file_get_contents($request->file('file')->getRealPath());
            if ($pdfContent === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membaca file yang diupload.',
                ], 422);
            }

            $salt = random_bytes(16);
            $iv = random_bytes(12);
            $tag = '';

            $key = $this->deriveSlipKey($request->encryption_code, $salt, 'v1');

            $encryptedBinary = openssl_encrypt(
                $pdfContent,
                'AES-256-GCM',
                $key,
                OPENSSL_RAW_DATA,
                $iv,
                $tag
            );

            if (function_exists('sodium_memzero')) {
                sodium_memzero($key);
            }

            if ($encryptedBinary === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengenkripsi file.',
                ], 500);
            }

            $encryptedBase64 = base64_encode($encryptedBinary);

            DB::connection('db_payslip')->beginTransaction();

            $slip = PayrollSlip::create([
                'payroll_period_id' => $period->id,
                'payroll_period_employee_id' => $employee->id,
                'file_name' => $request->file('file')->getClientOriginalName(),
                'encrypted_file' => $encryptedBase64,
                'original_file_size' => $request->file('file')->getSize(),
                'encrypted_file_size' => strlen($encryptedBase64),
                'mime_type' => 'application/pdf',
                'encryption_algorithm' => 'AES-256-GCM',
                'encryption_version' => 'v1',
                'encryption_salt' => bin2hex($salt),
                'encryption_iv' => bin2hex($iv),
                'encryption_tag' => bin2hex($tag),
                'status' => 'ACTIVE',
                'uploaded_by' => session('username'),
                'uploaded_at' => now(),
            ]);

            $period->total_uploaded = PayrollSlip::where('payroll_period_id', $period->id)
                ->where('status', 'ACTIVE')
                ->count();
            $period->save();

            DB::connection('db_payslip')->commit();

            return response()->json([
                'success' => true,
                'message' => 'Slip gaji ' . $employee->nama . ' periode ' . $period->period_name . ' berhasil diupload.',
                'slip_id' => $slip->id,
            ]);

        } catch (\Exception $e) {
            DB::connection('db_payslip')->rollBack();

            Log::error('Upload slip gaji gagal', [
                'period_id' => $request->period_id,
                'employee_id' => $request->employee_id,
                'file' => $request->hasFile('file') ? $request->file('file')->getClientOriginalName() : null,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
            ], 500);
        }
    }

    public function previewSlip(Request $request)
    {
        $request->validate([
            'slip_id' => 'required|integer',
            'encryption_code' => 'required|string',
        ]);

        $slip = PayrollSlip::where('id', $request->slip_id)
            ->where('status', 'ACTIVE')
            ->first();
        if (!$slip) {
            return response()->json([
                'success' => false,
                'message' => 'Slip gaji tidak ditemukan atau sudah tidak aktif.',
            ], 404);
        }

        $storedBinary = base64_decode($slip->encrypted_file, true);
        if ($storedBinary === false) {
            Log::error('Preview slip gagal: data terenkripsi rusak', [
                'slip_id' => $slip->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Data slip rusak dan tidak dapat dibuka.',
            ], 500);
        }

        $key = $this->deriveSlipKey(
            $request->encryption_code,
            hex2bin($slip->encryption_salt),
            $slip->encryption_version
        );

        $decrypted = openssl_decrypt(
            $storedBinary,
            $slip->encryption_algorithm ?: 'AES-256-GCM',
            $key,
            OPENSSL_RAW_DATA,
            hex2bin($slip->encryption_iv),
            hex2bin($slip->encryption_tag)
        );

        if (function_exists('sodium_memzero')) {
            sodium_memzero($key);
        }

        if ($decrypted === false) {
            Log::warning('Preview slip gagal: kode enkripsi salah', [
                'slip_id' => $slip->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Kode enkripsi salah. Silakan coba lagi.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'file_name' => $slip->file_name,
            'mime_type' => $slip->mime_type ?: 'application/pdf',
            'pdf_base64' => base64_encode($decrypted),
        ]);
    }

    public function payslipView(Request $request)
    {
        $nik = session('nik');

        $slipColumns = $this->slipListColumns();

        $rows = PayrollPeriodEmployee::with(['period', 'slip' => function ($q) use ($slipColumns) {
                $q->where('status', 'ACTIVE')->select($slipColumns);
            }])
            ->where('nik', $nik)
            ->join('payroll_periods', 'payroll_period_employees.payroll_period_id', '=', 'payroll_periods.id')
            ->orderBy('payroll_periods.period_year', 'desc')
            ->orderBy('payroll_periods.period_month', 'desc')
            ->select('payroll_period_employees.*')
            ->get();

        $salaryData = $rows->map(function ($emp) {
            $hasSlip = (bool) $emp->slip;

            return [
                'id' => $hasSlip ? $emp->slip->id : null,
                'period' => $emp->period ? $emp->period->period_name : '-',
                'uploadDate' => ($hasSlip && $emp->slip->uploaded_at)
                    ? $emp->slip->uploaded_at->format('d M Y H:i')
                    : '-',
                'fileName' => $hasSlip ? $emp->slip->file_name : '',
                'status' => $hasSlip ? 'available' : 'unavailable',
            ];
        })->values()->toArray();

        $available = array_values(array_filter($salaryData, function ($item) {
            return $item['status'] === 'available';
        }));

        $totalSlip = count($available);
        $latestPeriod = count($available) > 0 ? $available[0]['period'] : '-';

        return view('payslip.payslip-view.index', compact('salaryData', 'totalSlip', 'latestPeriod'));
    }

    public function getPeriods()
    {
        $periods = PayrollPeriod::orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $periods,
        ]);
    }

    public function deletePeriod($id)
    {
        $period = PayrollPeriod::find($id);

        if (!$period) {
            return response()->json([
                'success' => false,
                'message' => 'Periode tidak ditemukan.',
            ], 404);
        }

        try {
            DB::connection('db_payslip')->beginTransaction();

            $employeeIds = PayrollPeriodEmployee::where('payroll_period_id', $period->id)->pluck('id');
            if ($employeeIds->isNotEmpty()) {
                PayrollSlip::whereIn('payroll_period_employee_id', $employeeIds)->delete();
            }
            PayrollPeriodEmployee::where('payroll_period_id', $period->id)->delete();
            $period->delete();

            DB::connection('db_payslip')->commit();

            return response()->json([
                'success' => true,
                'message' => 'Periode ' . $period->period_name . ' berhasil dihapus.',
            ]);

        } catch (\Exception $e) {
            DB::connection('db_payslip')->rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data.',
            ], 500);
        }
    }

    public function deleteEmployee($id)
    {
        $employee = PayrollPeriodEmployee::find($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan.',
            ], 404);
        }

        try {
            DB::connection('db_payslip')->beginTransaction();

            PayrollSlip::where('payroll_period_employee_id', $employee->id)->delete();

            $periodId = $employee->payroll_period_id;
            $nama = $employee->nama;

            $employee->delete();

            $period = PayrollPeriod::find($periodId);
            if ($period) {
                $period->total_employees = PayrollPeriodEmployee::where('payroll_period_id', $periodId)->count();
                $period->total_uploaded = PayrollSlip::where('payroll_period_id', $periodId)
                    ->where('status', 'ACTIVE')
                    ->count();
                $period->save();
            }

            DB::connection('db_payslip')->commit();

            return response()->json([
                'success' => true,
                'message' => 'Data ' . $nama . ' berhasil dihapus.',
            ]);

        } catch (\Exception $e) {
            DB::connection('db_payslip')->rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data.',
            ], 500);
        }
    }

    public function deleteSlip($id)
    {
        $slip = PayrollSlip::find($id);

        if (!$slip) {
            return response()->json([
                'success' => false,
                'message' => 'File slip gaji tidak ditemukan.',
            ], 404);
        }

        try {
            DB::connection('db_payslip')->beginTransaction();

            $periodId = $slip->payroll_period_id;
            $nama = $slip->employee ? $slip->employee->nama : '';

            $slip->delete();

            $period = PayrollPeriod::find($periodId);
            if ($period) {
                $period->total_uploaded = PayrollSlip::where('payroll_period_id', $periodId)
                    ->where('status', 'ACTIVE')
                    ->count();
                $period->save();
            }

            DB::connection('db_payslip')->commit();

            return response()->json([
                'success' => true,
                'message' => 'File PDF slip gaji ' . $nama . ' berhasil dihapus.',
            ]);

        } catch (\Exception $e) {
            DB::connection('db_payslip')->rollBack();

            Log::error('Hapus file slip gagal', [
                'slip_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data.',
            ], 500);
        }
    }

    public function getSummary()
    {
        $totalPeriods = PayrollPeriod::count();
        $totalEmployees = PayrollPeriodEmployee::count();

        $totalUploaded = PayrollPeriodEmployee::whereHas('slip', function ($q) {
            $q->where('status', 'ACTIVE');
        })->count();
        $totalNotUploaded = $totalEmployees - $totalUploaded;

        return response()->json([
            'success' => true,
            'data' => [
                'total_periods' => $totalPeriods,
                'total_employees' => $totalEmployees,
                'total_uploaded' => $totalUploaded,
                'total_not_uploaded' => $totalNotUploaded,
            ],
        ]);
    }
}
