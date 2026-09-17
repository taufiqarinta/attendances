<?php

namespace App\Http\Controllers\Orientation;

use App\Http\Controllers\Controller;
use App\Models\Orientation\MasterOrientationActivity;
use App\Models\Orientation\MasterOrientationCategory;
use App\Models\Orientation\MasterPlant;
use App\Models\Orientation\MasterReaksiEvaluasi;
use App\Models\Orientation\OrientationActivity;
use App\Models\Orientation\OrientationActivityReaction;
use App\Models\Orientation\OrientationProgram;
use App\Models\Orientation\UserAccessOrientation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrientationProgramController extends Controller
{
    /**
     * Database connection name for development/test database
     */
    protected $dbConnection = 'hris_kobin';

    /**
     * Daftar orientation program dari database hris_kobin.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;
        $search = trim((string) $request->input('search'));

        // === Filter params ===
        $categoryId = $request->input('category_id');
        $status     = $request->input('status');
        $plantId    = $request->input('plant_id');
        $periodFrom = $request->input('period_from');
        $periodTo   = $request->input('period_to');

        $canManage = $this->canManageOrientation();
        $userNik   = $this->currentUserNik();

        // === MAIN QUERY ===
        $programs = OrientationProgram::query()
            ->with(['plant', 'activities', 'category'])
            ->when(!$canManage, function ($query) use ($userNik) {
                $query->where(function ($query) use ($userNik) {
                    $query->whereJsonContains('participants', ['nik' => $userNik])
                        ->orWhereJsonContains('hr_pic', ['nik' => $userNik]);
                });
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('batch_name', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhereHas('plant', function ($plantQuery) use ($search) {
                            $plantQuery->where('name_plant', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                });
            })
            ->when(!empty($categoryId), fn($q) => $q->where('category_id', $categoryId))
            ->when(!empty($status),     fn($q) => $q->where('status', $status))
            ->when(!empty($plantId),    fn($q) => $q->where('master_plants_id', $plantId))
            ->when(!empty($periodFrom), function ($q) use ($periodFrom) {
                $q->whereHas('activities', fn($a) => $a->whereDate('activity_date', '>=', $periodFrom));
            })
            ->when(!empty($periodTo), function ($q) use ($periodTo) {
                $q->whereHas('activities', fn($a) => $a->whereDate('activity_date', '<=', $periodTo));
            })
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        // === STATISTICS ===
        $allPrograms = OrientationProgram::select(['id', 'participants', 'status'])
            ->when(!$canManage, function ($query) use ($userNik) {
                $query->where(function ($query) use ($userNik) {
                    $query->whereJsonContains('participants', ['nik' => $userNik])
                        ->orWhereJsonContains('hr_pic', ['nik' => $userNik]);
                });
            })
            ->when(!empty($categoryId), fn($q) => $q->where('category_id', $categoryId))
            ->when(!empty($plantId),    fn($q) => $q->where('master_plants_id', $plantId))
            ->get();

        $statistics = [
            'total_programs'     => $allPrograms->count(),
            'total_participants' => $allPrograms->sum(fn($p) => count($p->participants ?? [])),
            'active_programs'    => $allPrograms->where('status', 'active')->count(),
            'completed_programs' => $allPrograms->where('status', 'completed')->count(),
        ];

        // === DROPDOWN DATA ===
        $categories = MasterOrientationCategory::where('status', 1)
            ->orderBy('category_name')
            ->get(['id', 'category_name', 'code_category']);

        $plants = MasterPlant::orderBy('name_plant')->get(['id', 'name_plant', 'code']);

        $statuses = [
            'active'    => 'Aktif',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        $hasActiveFilter = $search !== ''
            || !empty($categoryId)
            || !empty($status)
            || !empty($plantId)
            || !empty($periodFrom)
            || !empty($periodTo);

        // === RETURN VIEW ===
        return view('orientation.index', compact(
            'programs',
            'statistics',
            'search',
            'perPage',
            'canManage',
            'categories',
            'plants',
            'statuses',
            'categoryId',
            'status',
            'plantId',
            'periodFrom',
            'periodTo',
            'hasActiveFilter'
        ));
    }

    /**
     * Form Create Orientation
     */
    public function create()
    {
        $this->authorizeOrientationManager();

        $plants = MasterPlant::orderBy('name_plant', 'asc')->get();
        $masterOrientationActivities = MasterOrientationActivity::where('status', 1)
            ->orderBy('activity_name', 'asc')
            ->get();
        $categories = \App\Models\Orientation\MasterOrientationCategory::where('status', 1) // <-- TAMBAH
            ->orderBy('category_name', 'asc')
            ->get();

        // Ambil daftar HR PIC dari API users (contoh: filter role HR)
        $hrPics = [];
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)
                ->get('https://web.kobin.co.id/api/attendance/live/api_get_users.php');

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['success']) && !empty($data['data'])) {
                    $hrPics = collect($data['data'])
                        ->filter(fn($u) => str_contains(strtolower($u['jabatan'] ?? ''), 'hr')
                            || str_contains(strtolower($u['divisi'] ?? ''), 'hr'))
                        ->map(fn($u) => [
                            'nik' => (string) ($u['nik'] ?? ''),
                            'nama' => $u['nama'] ?? '',
                            'jabatan' => $u['jabatan'] ?? '',
                            'dept' => $u['dept'] ?? '',
                        ])
                        ->filter(fn($u) => $u['nik'] !== '')
                        ->values()
                        ->all();
                }
            }
        } catch (\Exception $e) {
            \Log::error('Gagal load HR PIC: ' . $e->getMessage());
        }


        return view('orientation.create-orientation', compact(
            'plants',
            'masterOrientationActivities',
            'categories',
            'hrPics'
        ));
    }

    /**
     * Form edit orientation. Plant tidak dapat diubah setelah program dibuat.
     */
    public function edit(OrientationProgram $orientation)
    {
        $this->authorizeOrientationManager();

        if ($this->hasStartedOrCompletedActivity($orientation)) {
            return redirect()
                ->route('orientation.index')
                ->with('edit_blocked', 'Program tidak dapat diedit karena terdapat kegiatan yang sudah dimulai atau selesai.');
        }

        $orientation->load(['plant', 'activities.masterActivity', 'category']);

        // Ambil semua NIK PIC dari kegiatan untuk diambil datanya sekaligus
        $niks = $orientation->activities->pluck('pic_employee_id')->filter()->unique()->values()->all();
        $allPicData = $this->getMultiplePicData($niks);

        $masterOrientationActivities = MasterOrientationActivity::where('status', 1)
            ->orderBy('activity_name', 'asc')
            ->get();
        $initialParticipants = $orientation->participants ?? [];
        $hrPics = $orientation->hr_pic ?? [];
        $hrPic = !empty($hrPics) ? $hrPics[0] : null;
        $category = $orientation->category;

        $kegiatanRows = $orientation->activities->map(function (OrientationActivity $activity) use ($allPicData) {
            $picNik = $activity->pic_employee_id;
            $picData = $allPicData[$picNik] ?? null;

            return [
                'id' => $activity->id,
                'activity_id' => $activity->master_orientation_activitie_id,
                'title' => $activity->masterActivity?->activity_name ?? 'Kegiatan',
                'description' => $activity->masterActivity?->description ?? '',
                'tanggal' => $activity->activity_date?->format('Y-m-d'),
                'waktu_mulai' => $activity->start_time?->format('H:i'),
                'waktu_selesai' => $activity->end_time?->format('H:i'),
                'pic' => $picData['nama'] ?? $picNik,
                'pic_nik' => $picNik,
                'jabatan' => $picData['jabatan'] ?? '',
                'icon' => $this->getActivityIcon($activity->masterActivity?->activity_name ?? ''),
                'color' => $this->getActivityColor($activity->masterActivity?->activity_name ?? ''),
            ];
        })->values()->all();

        return view('orientation.edit-orientation', compact(
            'orientation',
            'masterOrientationActivities',
            'initialParticipants',
            'kegiatanRows',
            'hrPic',
            'category'
        ));
    }

    /**
     * Perbarui peserta dan rincian kegiatan tanpa mengubah plant program.
     */
    public function update(Request $request, OrientationProgram $orientation)
    {
        $this->authorizeOrientationManager();

        // Periksa ulang di server agar pembatasan edit tidak dapat dilewati
        // melalui URL atau request langsung.
        if ($this->hasStartedOrCompletedActivity($orientation)) {
            return response()->json([
                'message' => 'Program tidak dapat diedit karena terdapat kegiatan yang sudah dimulai atau selesai.',
            ], 422);
        }

        $request->merge(['participants' => $this->normalizeParticipants($request->input('participants', []))]);

        $request->validate([
            'participants' => 'required|array|min:1',
            'participants.*.nik' => 'required|string|max:100',
            'participants.*.nama' => 'required|string|max:255',
            'participants.*.jabatan' => 'nullable|string|max:255',
            'participants.*.dept' => 'nullable|string|max:255',
            'activities' => 'required|array|min:1',
            'activities.*.activity_id' => 'required|exists:hris_kobin.master_orientation_activities,id',
            'activities.*.tanggal' => 'required|date',
            'activities.*.waktu_mulai' => 'required',
            'activities.*.waktu_selesai' => 'required',
            'activities.*.pic' => 'required|string|max:255',
            'activities.*.pic_nik' => 'required|string|max:100',
        ]);

        $activityIds = collect($request->input('activities'))->pluck('activity_id')->unique();
        $allowedActivityIds = MasterOrientationActivity::whereIn('id', $activityIds)
            ->where('status', true)
            ->pluck('id');

        if ($allowedActivityIds->count() !== $activityIds->count()) {
            return response()->json(['message' => 'Terdapat kegiatan yang tidak valid.'], 422);
        }

        DB::connection($this->dbConnection)->transaction(function () use ($request, $orientation) {
            $orientation->update(['participants' => array_values($request->participants)]);
            $orientation->activities()->delete();

            foreach ($request->activities as $activity) {
                OrientationActivity::create([
                    'orientation_id' => $orientation->id,
                    'master_orientation_activitie_id' => $activity['activity_id'],
                    'activity_date' => $activity['tanggal'],
                    'start_time' => $activity['waktu_mulai'],
                    'end_time' => $activity['waktu_selesai'],
                    'pic_employee_id' => $activity['pic_nik'],
                    'status' => 'pending',
                ]);
            }
        });

        return response()->json([
            'message' => 'Orientation berhasil diperbarui.',
            'redirect' => route('orientation.index'),
        ]);
    }

    /**
     * Store Orientation Program
     */
    public function store(Request $request)
    {
        $this->authorizeOrientationManager();

        // Normalisasi hr_pic & participants
        $request->merge([
            'participants' => $this->normalizeParticipants($request->input('participants', [])),
            'hr_pic' => $this->normalizeHrPic($request->input('hr_pic', [])), // <-- TAMBAH
        ]);

        $request->validate([
            'category_id' => 'required|exists:hris_kobin.master_orientation_categories,id', // <-- TAMBAH
            'plant_id' => 'required|exists:hris_kobin.master_plants,id',
            'hr_pic' => 'required|array|min:1', // <-- TAMBAH
            'hr_pic.*.nik' => 'required|string|max:100',
            'hr_pic.*.nama' => 'required|string|max:255',
            'participants' => 'required|array|min:1',
            'participants.*.nik' => 'required|string|max:100',
            'participants.*.nama' => 'required|string|max:255',
            'participants.*.jabatan' => 'nullable|string|max:255',
            'participants.*.dept' => 'nullable|string|max:255',
            'activities' => 'required|array|min:1',
            'activities.*.activity_id' => 'required|exists:hris_kobin.master_orientation_activities,id',
            'activities.*.tanggal' => 'required|date',
            'activities.*.waktu_mulai' => 'required',
            'activities.*.waktu_selesai' => 'required',
            'activities.*.pic' => 'required|string|max:255',
            'activities.*.pic_nik' => 'required|string|max:100',
            'activities.*.jabatan' => 'nullable|string|max:255',
        ]);

        $activityIds = collect($request->input('activities'))->pluck('activity_id')->unique();
        $allowedActivityIds = MasterOrientationActivity::whereIn('id', $activityIds)
            ->where('status', true)
            ->pluck('id');

        if ($allowedActivityIds->count() !== $activityIds->count()) {
            return response()->json([
                'message' => 'Terdapat kegiatan yang tidak valid atau tidak aktif.',
            ], 422);
        }

        if ($allowedActivityIds->count() !== $activityIds->count()) {
            return response()->json([
                'message' => 'Terdapat kegiatan yang tidak tersedia untuk plant yang dipilih.',
            ], 422);
        }

        $orientation = DB::connection($this->dbConnection)->transaction(function () use ($request) {
            $totalPrograms = OrientationProgram::count();
            $nextBatchNumber = $totalPrograms + 1;
            $batchName = 'Kobin Orientation Program Batch ' . $nextBatchNumber;

            $orientation = OrientationProgram::create([
                'category_id' => $request->category_id,        // <-- TAMBAH
                'batch_name' => $batchName,
                'master_plants_id' => $request->plant_id,
                'participants' => array_values($request->participants),
                'hr_pic' => array_values($request->hr_pic),    // <-- TAMBAH
                'status' => 'active',
            ]);

            foreach ($request->activities as $activity) {
                OrientationActivity::create([
                    'orientation_id' => $orientation->id,
                    'master_orientation_activitie_id' => $activity['activity_id'],
                    'activity_date' => $activity['tanggal'],
                    'start_time' => $activity['waktu_mulai'],
                    'end_time' => $activity['waktu_selesai'],
                    'pic_employee_id' => $activity['pic_nik'],
                    'status' => 'pending',
                ]);
            }

            return $orientation;
        });

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Orientation program berhasil dibuat.',
                'redirect' => route('orientation.index'),
                'id' => $orientation->id,
                'batch_name' => $orientation->batch_name,
            ]);
        }

        return redirect()->route('orientation.index')
            ->with('success', 'Orientation program berhasil dibuat.');
    }

    /**
     * Simpan snapshot HR PIC agar nama dapat ditampilkan tanpa API.
     */
    private function normalizeHrPic(mixed $hrPic): array
    {
        if (!is_array($hrPic)) {
            return [];
        }

        return collect($hrPic)
            ->map(function ($pic) {
                if (is_string($pic) || is_numeric($pic)) {
                    return ['nik' => (string) $pic, 'nama' => (string) $pic];
                }

                return [
                    'nik' => (string) ($pic['nik'] ?? ''),
                    'nama' => (string) ($pic['nama'] ?? ''),
                    'jabatan' => $pic['jabatan'] ?? null,
                    'dept' => $pic['dept'] ?? null,
                ];
            })
            ->filter(fn(array $pic) => $pic['nik'] !== '')
            ->unique('nik')
            ->values()
            ->all();
    }

    /**
     * Hapus program orientation beserta seluruh kegiatan terkait.
     */
    public function destroy(OrientationProgram $orientation)
    {
        $this->authorizeOrientationManager();

        DB::connection($this->dbConnection)->transaction(function () use ($orientation) {
            $orientation->activities()->delete();
            $orientation->delete();
        });

        return redirect()
            ->route('orientation.index')
            ->with('deleted_success', 'Orientation program berhasil dihapus.');
    }

    /**
     * Program terkunci setelah salah satu kegiatannya dimulai atau selesai.
     */
    private function hasStartedOrCompletedActivity(OrientationProgram $orientation): bool
    {
        return $orientation->activities()
            ->where(function ($query) {
                $query->whereIn('status', ['ongoing', 'completed'])
                    ->orWhereNotNull('started_at')
                    ->orWhereNotNull('completed_at');
            })
            ->exists();
    }

    /**
     * Simpan snapshot peserta agar nama dapat ditampilkan tanpa memanggil API.
     */
    private function normalizeParticipants(mixed $participants): array
    {
        if (!is_array($participants)) {
            return [];
        }

        return collect($participants)
            ->map(function ($participant) {
                if (is_string($participant) || is_numeric($participant)) {
                    return ['nik' => (string) $participant, 'nama' => (string) $participant];
                }

                return [
                    'nik' => (string) ($participant['nik'] ?? ''),
                    'nama' => (string) ($participant['nama'] ?? ''),
                    'jabatan' => $participant['jabatan'] ?? null,
                    'dept' => $participant['dept'] ?? null,
                ];
            })
            ->filter(fn(array $participant) => $participant['nik'] !== '')
            ->unique('nik')
            ->values()
            ->all();
    }

    /**
     * Tampilkan detail program orientation.
     */
    public function show(OrientationProgram $orientation)
    {
        $this->authorizeOrientationViewer($orientation);
        $canManage = $this->canManageOrientation($orientation);

        $orientation->load(['plant', 'activities.masterActivity']);

        // ============================================================
        // 1. AMBIL DATA PIC (untuk info trainer di kegiatan)
        // ============================================================
        $niks = $orientation->activities->pluck('pic_employee_id')->filter()->unique()->values()->all();
        $allPicData = $this->getMultiplePicData($niks);

        // ============================================================
        // 2. BUILD KEGIATAN ROWS
        // ============================================================
        $kegiatanRows = $orientation->activities->map(function (OrientationActivity $activity) use ($allPicData) {
            $picNik = $activity->pic_employee_id;
            $picData = $allPicData[$picNik] ?? null;

            return [
                'id' => $activity->id,
                'activity_id' => $activity->master_orientation_activitie_id,
                'title' => $activity->masterActivity?->activity_name ?? 'Kegiatan',
                'description' => $activity->masterActivity?->description ?? '',
                'tanggal' => $activity->activity_date?->format('Y-m-d'),
                'tanggal_formatted' => $activity->activity_date?->format('d M Y') ?? '-',
                'waktu_mulai' => $activity->start_time?->format('H:i') ?? '-',
                'waktu_selesai' => $activity->end_time?->format('H:i') ?? '-',
                'waktu_range' => ($activity->start_time?->format('H:i') ?? '-') . ' - ' . ($activity->end_time?->format('H:i') ?? '-'),
                'pic_nik' => $picNik,
                'pic_name' => $picData['nama'] ?? $picNik,
                'pic_jabatan' => $picData['jabatan'] ?? '-',
                'pic_dept' => $picData['dept'] ?? '-',
                'status' => $activity->status ?? 'pending',
                'status_label' => $this->getStatusLabel($activity->status ?? 'pending'),
                'status_color' => $this->getStatusColor($activity->status ?? 'pending'),
                'icon' => $this->getActivityIcon($activity->masterActivity?->activity_name ?? ''),
                'color' => $this->getActivityColor($activity->masterActivity?->activity_name ?? ''),
                'score' => $activity->score,
                'score_note' => $activity->score_note,
                'started_at' => $activity->started_at?->format('d M Y H:i'),
                'completed_at' => $activity->completed_at?->format('d M Y H:i'),
            ];
        })->values()->all();

        // ============================================================
        // 3. DATA PESERTA (dari JSON field)
        // ============================================================
        $participantsFromDb = $orientation->participants ?? [];
        $totalParticipants = count($participantsFromDb);

        // Ambil data lengkap peserta dari API
        $participantNiks = collect($participantsFromDb)->pluck('nik')->filter()->values()->all();
        $allParticipantData = $this->getMultiplePicData($participantNiks);

        // ============================================================
        // 4. BUILD $participantDetails (gabungan data peserta + attendance)
        // ============================================================
        $totalActivities = $orientation->activities->count();

        $participantDetails = collect($participantsFromDb)->map(function ($participant) use (
            $allParticipantData,
            $orientation,
            $totalActivities,
            $kegiatanRows
        ) {
            $nik = is_array($participant) ? ($participant['nik'] ?? null) : $participant;
            $data = $nik ? ($allParticipantData[$nik] ?? null) : null;

            // ---------------------------------------------------------
            // Agregasi attendance dari SEMUA kegiatan
            // ---------------------------------------------------------
            $hadirCount = 0;
            $preTestScores = [];
            $postTestScores = [];
            $notes = [];
            $breakdown = [];

            foreach ($orientation->activities as $activity) {
                $attendance = collect($activity->attendances ?? [])->firstWhere('nik', $nik);
                $kegiatanInfo = collect($kegiatanRows)->firstWhere('id', $activity->id);

                $isPresent = !empty($attendance['is_present']);
                $preTest = $attendance['pre_test'] ?? null;
                $postTest = $attendance['post_test'] ?? null;
                $note = $attendance['note'] ?? null;

                if ($isPresent) $hadirCount++;
                if ($preTest !== null && $preTest !== '') $preTestScores[] = (float) $preTest;
                if ($postTest !== null && $postTest !== '') $postTestScores[] = (float) $postTest;
                if (!empty($note)) $notes[] = $note;

                $breakdown[] = [
                    'activity_id' => $activity->id,
                    'title' => $kegiatanInfo['title'] ?? 'Kegiatan',
                    'tanggal' => $kegiatanInfo['tanggal_formatted'] ?? '-',
                    'is_present' => $isPresent,
                    'pre_test' => $preTest,
                    'post_test' => $postTest,
                    'note' => $note,
                ];
            }

            // ---------------------------------------------------------
            // Hitung status & progress
            // ---------------------------------------------------------
            $attendancePercentage = $totalActivities > 0
                ? round(($hadirCount / $totalActivities) * 100)
                : 0;

            // Status peserta: completed jika hadir 100%
            $status = 'pending';
            if ($attendancePercentage == 100 && $totalActivities > 0) {
                $status = 'completed';
            } elseif ($hadirCount > 0) {
                $status = 'active';
            }

            return [
                'nik' => $nik,
                'nama' => $data['nama'] ?? ($participant['nama'] ?? $nik),
                'jabatan' => $data['jabatan'] ?? ($participant['jabatan'] ?? '-'),
                'dept' => $data['dept'] ?? ($participant['dept'] ?? '-'),
                'email' => $data['email'] ?? ($participant['email'] ?? '-'),
                'join_date' => is_array($participant) ? ($participant['join_date'] ?? null) : null,

                // Agregat
                'hadir_count' => $hadirCount,
                'total_activities' => $totalActivities,
                'attendance_percentage' => $attendancePercentage,
                'pre_test_avg' => count($preTestScores) > 0
                    ? round(array_sum($preTestScores) / count($preTestScores), 1) : null,
                'post_test_avg' => count($postTestScores) > 0
                    ? round(array_sum($postTestScores) / count($postTestScores), 1) : null,
                'notes' => $notes,

                // Status
                'status' => $status,
                'progress' => $attendancePercentage,

                // Detail per kegiatan
                'activity_breakdown' => $breakdown,
            ];
        });

        // ============================================================
        // 5. STATISTIK PESERTA
        // ============================================================
        $participantStats = [
            'total' => $participantDetails->count(),
            'completed' => $participantDetails->where('status', 'completed')->count(),
            'active' => $participantDetails->where('status', 'active')->count(),
            'pending' => $participantDetails->where('status', 'pending')->count(),
        ];

        // ============================================================
        // 6. STATISTIK KEGIATAN
        // ============================================================
        $completedActivities = $orientation->activities->where('status', 'completed')->count();
        $pendingActivities = $orientation->activities->where('status', 'pending')->count();
        $inProgressActivities = $orientation->activities->where('status', 'in_progress')->count();

        // ============================================================
        // 7. KEGIATAN BY DATE
        // ============================================================
        $activitiesByDate = $orientation->activities
            ->groupBy(fn($activity) => $activity->activity_date?->format('Y-m-d') ?? 'no-date')
            ->map(function ($activities, $date) {
                return [
                    'date' => $date,
                    'date_formatted' => $date !== 'no-date' ? date('d M Y', strtotime($date)) : 'Tanggal tidak tersedia',
                    'activities' => $activities->map(fn($activity) => [
                        'id' => $activity->id,
                        'name' => $activity->masterActivity?->activity_name ?? 'Kegiatan',
                        'start_time' => $activity->start_time?->format('H:i') ?? '-',
                        'end_time' => $activity->end_time?->format('H:i') ?? '-',
                        'pic' => $activity->pic_employee_id,
                        'status' => $activity->status ?? 'pending',
                    ])->values()->all()
                ];
            })->values()->all();

        // ============================================================
        // 8. MASTER ACTIVITIES YANG TERSEDIA
        // ============================================================
        $availableActivities = MasterOrientationActivity::where('status', true)
            ->where(function ($query) use ($orientation) {
                $query->whereJsonContains('plants', (string) $orientation->master_plants_id)
                    ->orWhereNull('plants')
                    ->orWhere('plants', '[]');
            })
            ->orderBy('activity_name', 'asc')
            ->get();

        // ============================================================
        // 9. STATISTIK PROGRAM
        // ============================================================
        $programStats = [
            'total_activities' => $totalActivities,
            'completed_activities' => $completedActivities,
            'pending_activities' => $pendingActivities,
            'in_progress_activities' => $inProgressActivities,
            'total_participants' => $totalParticipants,
            'completion_percentage' => $totalActivities > 0
                ? round(($completedActivities / $totalActivities) * 100) : 0,
        ];

        $scheduleConflicts = $this->checkScheduleConflicts($orientation->activities);

        // Attendances map untuk modal kehadiran
        $attendancesByActivity = $orientation->activities->mapWithKeys(function ($activity) {
            return [$activity->id => collect($activity->attendances ?? [])->keyBy('nik')->all()];
        })->all();

        // ============================================================
        // ATTENDANCE + REACTION MAPS
        // ============================================================
        $attendancesByActivity = $orientation->activities->mapWithKeys(function ($activity) {
            return [$activity->id => collect($activity->attendances ?? [])->keyBy('nik')->all()];
        })->all();

        // ============================================================
        // REACTION ACCESS
        // ============================================================
        $userNik = $this->currentUserNik();
        $isManager = $this->canManageOrientation($orientation);
        $isParticipant = $this->isParticipantOf($orientation, $userNik);

        // Reaksi yang sudah diisi user (kalau peserta)
        $myReactionActivityIds = [];
        if ($isParticipant) {
            $activityIds = $orientation->activities->pluck('id')->map(fn($id) => (int) $id)->toArray();

            $myReactionActivityIds = OrientationActivityReaction::query()
                ->whereIn('orientation_activity_id', $activityIds)
                ->whereRaw(
                    "JSON_UNQUOTE(JSON_EXTRACT(employee_id, '$.nik')) = ?",
                    [(string) $userNik]
                )
                ->pluck('orientation_activity_id')
                ->unique()
                ->values()
                ->all();
        }



        // Info jumlah reaksi per kegiatan
        $reactionsByActivity = $orientation->activities->mapWithKeys(function ($activity) {
            $reactions = OrientationActivityReaction::where('orientation_activity_id', $activity->id)->get();
            return [$activity->id => [
                'count' => $reactions->count(),
                'respondents' => $reactions->pluck('participant.nik')->unique()->count(),
            ]];
        })->all();
        return view('orientation.detail-orientation', [
            'orientation' => $orientation,
            'kegiatanRows' => $kegiatanRows,
            'participants' => $participantsFromDb,
            'participantDetails' => $participantDetails,
            'participantStats' => $participantStats,
            'totalParticipants' => $totalParticipants,
            'totalActivities' => $totalActivities,
            'completedActivities' => $completedActivities,
            'pendingActivities' => $pendingActivities,
            'inProgressActivities' => $inProgressActivities,
            'activitiesByDate' => $activitiesByDate,
            'availableActivities' => $availableActivities,
            'programStats' => $programStats,
            'scheduleConflicts' => $scheduleConflicts,
            'canManage' => $canManage,
            'attendancesByActivity' => $attendancesByActivity,
            'canGiveReaction' => $isParticipant,        // peserta
            'canViewReactionResult' => $isManager,      // HR PIC / Admin
            'myReactionActivityIds' => $myReactionActivityIds, // yang sudah diisi
            'reactionsByActivity' => $reactionsByActivity,
        ]);
    }

    /**
     * Mendapatkan data multiple PIC sekaligus (lebih efisien)
     */
    private function getMultiplePicData(array $niks): array
    {
        if (empty($niks)) {
            return [];
        }

        $result = [];
        $needToFetch = [];

        // Cek cache terlebih dahulu
        foreach ($niks as $nik) {
            $cacheKey = 'pic_data_' . $nik;
            if (Cache::has($cacheKey)) {
                $result[$nik] = Cache::get($cacheKey);
            } else {
                $needToFetch[] = $nik;
            }
        }

        // Jika semua data sudah ada di cache
        if (empty($needToFetch)) {
            return $result;
        }

        try {
            // Panggil API sekali untuk semua data
            $response = Http::timeout(5)->get('https://web.kobin.co.id/api/attendance/live/api_get_users.php');

            if ($response->successful()) {
                $data = $response->json();

                if ($data['success'] && isset($data['data'])) {
                    $users = collect($data['data']);

                    foreach ($needToFetch as $nik) {
                        $user = $users->firstWhere('nik', $nik);

                        if ($user) {
                            $picData = [
                                'nik' => $user['nik'] ?? '',
                                'nama' => $user['nama'] ?? '',
                                'jabatan' => $user['jabatan'] ?? '-',
                                'dept' => $user['dept'] ?? '-',
                                'plant' => $user['plant'] ?? '',
                                'email' => $user['email'] ?? '',
                                'divisi' => $user['divisi'] ?? '',
                            ];

                            // Simpan ke cache
                            $cacheKey = 'pic_data_' . $nik;
                            Cache::put($cacheKey, $picData, 3600);

                            $result[$nik] = $picData;
                        } else {
                            // Data tidak ditemukan, simpan data kosong agar tidak dipanggil lagi
                            $picData = [
                                'nik' => $nik,
                                'nama' => $nik,
                                'jabatan' => '-',
                                'dept' => '-',
                                'plant' => '',
                                'email' => '',
                                'divisi' => '',
                            ];

                            $cacheKey = 'pic_data_' . $nik;
                            Cache::put($cacheKey, $picData, 300); // Cache lebih pendek untuk data tidak ditemukan

                            $result[$nik] = $picData;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data PIC: ' . $e->getMessage());

            // Fallback untuk data yang gagal diambil
            foreach ($needToFetch as $nik) {
                $result[$nik] = [
                    'nik' => $nik,
                    'nama' => $nik,
                    'jabatan' => '-',
                    'dept' => '-',
                    'plant' => '',
                    'email' => '',
                    'divisi' => '',
                ];
            }
        }

        return $result;
    }

    /**
     * Mendapatkan nama PIC dari API berdasarkan NIK (digunakan untuk single request)
     */
    private function getPicName(?string $picNik): string
    {
        if (!$picNik) {
            return '-';
        }

        $cacheKey = 'pic_name_' . $picNik;
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::timeout(5)->get('https://web.kobin.co.id/api/attendance/live/api_get_users.php');

            if ($response->successful()) {
                $data = $response->json();

                if ($data['success'] && isset($data['data'])) {
                    $user = collect($data['data'])->firstWhere('nik', $picNik);

                    if ($user) {
                        $nama = $user['nama'] ?? $picNik;
                        Cache::put($cacheKey, $nama, 3600);
                        return $nama;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data PIC: ' . $e->getMessage());
        }

        return $picNik;
    }

    /**
     * Mendapatkan data lengkap PIC dari API (digunakan untuk single request)
     */
    private function getPicData(?string $picNik): ?array
    {
        if (!$picNik) {
            return null;
        }

        $cacheKey = 'pic_data_' . $picNik;
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::timeout(5)->get('https://web.kobin.co.id/api/attendance/live/api_get_users.php');

            if ($response->successful()) {
                $data = $response->json();

                if ($data['success'] && isset($data['data'])) {
                    $user = collect($data['data'])->firstWhere('nik', $picNik);

                    if ($user) {
                        $picData = [
                            'nik' => $user['nik'] ?? '',
                            'nama' => $user['nama'] ?? '',
                            'jabatan' => $user['jabatan'] ?? '-',
                            'dept' => $user['dept'] ?? '-',
                            'plant' => $user['plant'] ?? '',
                            'email' => $user['email'] ?? '',
                            'divisi' => $user['divisi'] ?? '',
                        ];

                        Cache::put($cacheKey, $picData, 3600);
                        return $picData;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data PIC: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Mendapatkan label status kegiatan
     */
    private function getStatusLabel(string $status): string
    {
        $labels = [
            'pending' => 'Menunggu',
            'in_progress' => 'Berlangsung',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        return $labels[$status] ?? ucfirst($status);
    }

    /**
     * Mendapatkan warna status kegiatan
     */
    private function getStatusColor(string $status): string
    {
        $colors = [
            'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
            'in_progress' => 'bg-blue-50 text-blue-700 border-blue-100',
            'completed' => 'bg-green-50 text-green-700 border-green-100',
            'cancelled' => 'bg-red-50 text-red-700 border-red-100',
        ];

        return $colors[$status] ?? 'bg-gray-50 text-gray-700 border-gray-100';
    }

    /**
     * Mendapatkan ikon berdasarkan nama kegiatan
     */
    private function getActivityIcon(string $activityName): string
    {
        $icons = [
            'briefing' => '📋',
            'training' => '👨‍🏫',
            'workshop' => '🔧',
            'presentation' => '📊',
            'orientation' => '🎯',
            'introduction' => '👋',
            'tour' => '🏭',
            'meeting' => '💡',
            'assessment' => '📝',
            'evaluation' => '⭐',
        ];

        $name = strtolower($activityName);
        foreach ($icons as $key => $icon) {
            if (strpos($name, $key) !== false) {
                return $icon;
            }
        }

        return '📋';
    }

    /**
     * Mendapatkan warna berdasarkan nama kegiatan
     */
    private function getActivityColor(string $activityName): string
    {
        $colors = [
            'bg-red-50 text-red-600',
            'bg-orange-50 text-orange-600',
            'bg-green-50 text-green-600',
            'bg-purple-50 text-purple-600',
            'bg-blue-50 text-blue-600',
            'bg-yellow-50 text-yellow-600',
            'bg-pink-50 text-pink-600',
            'bg-indigo-50 text-indigo-600',
        ];

        $index = abs(crc32($activityName)) % count($colors);
        return $colors[$index];
    }

    /**
     * Cek konflik jadwal antar kegiatan
     */
    private function checkScheduleConflicts($activities): array
    {
        $conflicts = [];
        $grouped = [];

        foreach ($activities as $activity) {
            $date = $activity->activity_date?->format('Y-m-d');
            if (!$date) continue;

            $grouped[$date][] = $activity;
        }

        foreach ($grouped as $date => $items) {
            for ($i = 0; $i < count($items); $i++) {
                for ($j = $i + 1; $j < count($items); $j++) {
                    $start1 = $items[$i]->start_time?->format('H:i') ?? '00:00';
                    $end1 = $items[$i]->end_time?->format('H:i') ?? '00:00';
                    $start2 = $items[$j]->start_time?->format('H:i') ?? '00:00';
                    $end2 = $items[$j]->end_time?->format('H:i') ?? '00:00';

                    if ($start1 < $end2 && $start2 < $end1) {
                        $conflicts[] = [
                            'date' => $date,
                            'activity1' => $items[$i]->masterActivity?->activity_name ?? 'Kegiatan',
                            'activity2' => $items[$j]->masterActivity?->activity_name ?? 'Kegiatan',
                            'time1' => "$start1 - $end1",
                            'time2' => "$start2 - $end2",
                        ];
                    }
                }
            }
        }

        return $conflicts;
    }
    /**
     * Update nilai kegiatan
     */
    public function updateScore(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:hris_kobin.orientation_activities,id',
                'score' => 'required|numeric|min:0|max:100',
                'score_note' => 'nullable|string|max:255',
            ]);

            $activity = OrientationActivity::findOrFail($request->id);
            if (!$this->canManageOrientation($activity->orientation)) {
                return response()->json(['message' => 'Anda tidak memiliki akses untuk mengubah nilai kegiatan.'], 403);
            }

            $activity->update([
                'score' => $request->score,
                'score_note' => $request->score_note,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Nilai berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Update status kegiatan
     */
    public function updateStatus(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:hris_kobin.orientation_activities,id',
                'status' => 'required|in:pending,ongoing,completed,cancelled',
            ]);

            $activity = OrientationActivity::findOrFail($request->id);
            if (!$this->canManageOrientation($activity->orientation)) {
                return response()->json(['message' => 'Anda tidak memiliki akses untuk mengubah status kegiatan.'], 403);
            }


            // Data yang akan diupdate
            $data = ['status' => $request->status];

            // Jika status menjadi ongoing, set started_at
            if ($request->status === 'ongoing' && !$activity->started_at) {
                $data['started_at'] = now();
            }

            // Jika status menjadi completed, set completed_at
            if ($request->status === 'completed' && !$activity->completed_at) {
                $data['completed_at'] = now();
            }

            // Jika status dibatalkan, kosongkan started_at dan completed_at
            if ($request->status === 'cancelled') {
                $data['started_at'] = null;
                $data['completed_at'] = null;
            }

            $activity->update($data);

            // ================================================================
            // CEK APAKAH SEMUA KEGIATAN SUDAH SELESAI
            // ================================================================
            $orientation = $activity->orientation;
            $totalActivities = $orientation->activities()->count();
            $completedActivities = $orientation->activities()->where('status', 'completed')->count();

            // Jika semua kegiatan sudah selesai (completed) dan program belum completed/cancelled
            if ($totalActivities > 0 && $totalActivities === $completedActivities) {
                // Cek apakah program sudah active atau pending
                if (in_array($orientation->status, ['active', 'pending'])) {
                    $orientation->update(['status' => 'completed']);

                    \Log::info('Program otomatis selesai', [
                        'orientation_id' => $orientation->id,
                        'total_activities' => $totalActivities,
                        'completed_activities' => $completedActivities
                    ]);
                }
            }
            // ================================================================

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diupdate',
                'data' => [
                    'status' => $activity->status,
                    'started_at' => $activity->started_at,
                    'completed_at' => $activity->completed_at,
                    'program_status' => $orientation->fresh()->status,
                    'program_completed' => $totalActivities === $completedActivities
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error update status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get score data for an activity
     */
    public function getScore(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:hris_kobin.orientation_activities,id'
            ]);

            $activity = OrientationActivity::find($request->id);

            if (!$activity || !$this->canViewOrientation($activity->orientation)) {
                return response()->json(['message' => 'Anda tidak memiliki akses ke data kegiatan ini.'], 403);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'score' => $activity->score,
                    'score_note' => $activity->score_note
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Batalkan seluruh program orientation
     * Mengubah status program menjadi 'cancelled' dan semua kegiatan menjadi 'cancelled'
     */
    public function cancel(OrientationProgram $orientation)
    {
        $this->authorizeOrientationManager($orientation);

        try {
            DB::connection($this->dbConnection)->transaction(function () use ($orientation) {
                // Update status program menjadi cancelled
                $orientation->update(['status' => 'cancelled']);

                // Update semua kegiatan menjadi cancelled
                $orientation->activities()->update([
                    'status' => 'cancelled',
                    'completed_at' => null,
                    'started_at' => null,
                ]);
            });

            return redirect()
                ->route('orientation.index')
                ->with('success', 'Program orientation berhasil dibatalkan.');
        } catch (\Exception $e) {
            \Log::error('Error cancel program: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Gagal membatalkan program: ' . $e->getMessage());
        }
    }

    /**
     * Tandai program selesai
     * Mengubah status program menjadi 'completed' dan semua kegiatan menjadi 'completed'
     */
    public function complete(OrientationProgram $orientation)
    {
        $this->authorizeOrientationManager($orientation);

        try {
            DB::connection($this->dbConnection)->transaction(function () use ($orientation) {
                // Update status program menjadi completed
                $orientation->update(['status' => 'completed']);

                // Update semua kegiatan menjadi completed
                $orientation->activities()->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            });

            return redirect()
                ->route('orientation.index')
                ->with('success', 'Program orientation berhasil ditandai selesai.');
        } catch (\Exception $e) {
            \Log::error('Error complete program: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Gagal menandai program selesai: ' . $e->getMessage());
        }
    }
    /**
     * Export jadwal ke PDF
     */
    public function exportSchedule(OrientationProgram $orientation)
    {
        $this->authorizeOrientationManager($orientation);

        $orientation->load(['plant', 'activities.masterActivity']);

        $participants = $orientation->participants ?? [];
        $totalParticipants = count($participants);

        // Ambil semua NIK PIC dari kegiatan
        $niks = $orientation->activities->pluck('pic_employee_id')->filter()->unique()->values()->all();
        $allPicData = $this->getMultiplePicData($niks);

        // Kelompokkan kegiatan berdasarkan tanggal
        $groupedActivities = $orientation->activities
            ->groupBy(function ($activity) {
                return $activity->activity_date?->format('Y-m-d');
            })
            ->map(function ($activities, $date) use ($allPicData) {
                return [
                    'date' => $date,
                    'date_formatted' => $activities->first()->activity_date?->format('l, d F Y') ?? $date,
                    'rows' => $activities->sortBy('start_time')->map(function ($activity) use ($allPicData) {
                        $picNik = $activity->pic_employee_id;
                        $picData = $allPicData[$picNik] ?? null;

                        return [
                            'time' => ($activity->start_time?->format('H.i') ?? '-') . ' - ' . ($activity->end_time?->format('H.i') ?? '-'),
                            'activity' => $activity->masterActivity?->activity_name ?? 'Kegiatan',
                            'pic_nik' => $picNik,
                            'pic_name' => $picData['nama'] ?? $picNik,
                            'position' => $picData['jabatan'] ?? '-',
                        ];
                    })->values()->all()
                ];
            })->values()->all();

        // Ambil batch number
        $batchNumber = $this->extractBatchNumber($orientation->batch_name);

        // Ambil periode dari kegiatan pertama dan terakhir
        $firstActivity = $orientation->activities->first();
        $lastActivity = $orientation->activities->last();
        $period = '';
        if ($firstActivity && $lastActivity) {
            $startDate = $firstActivity->activity_date?->format('d F Y') ?? '-';
            $endDate = $lastActivity->activity_date?->format('d F Y') ?? '-';
            $period = $startDate . ' - ' . $endDate;
        }

        $data = [
            'orientation' => $orientation,
            'groupedActivities' => $groupedActivities,
            'participants' => $participants,
            'totalParticipants' => $totalParticipants,
            'plantName' => $orientation->plant->name_plant ?? '-',
            'batchNumber' => $batchNumber,
            'period' => $period,
        ];

        $pdf = Pdf::loadView('orientation.exports.schedule-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'times',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        return $pdf->download('Jadwal_Orientation_' . str_replace(' ', '_', $orientation->batch_name) . '.pdf');
    }

    /**
     * Extract batch number from batch name
     */
    private function extractBatchNumber($batchName)
    {
        preg_match('/Batch (\d+)/', $batchName, $matches);
        return $matches[1] ?? '-';
    }

    /**
     * Get PIC position from API or database
     */
    private function getPicPosition($picNik)
    {
        // Coba ambil dari cache atau API
        $picData = $this->getPicData($picNik);
        return $picData['jabatan'] ?? '-';
    }

    /**
     * Export peserta ke PDF
     */
    public function exportParticipants(OrientationProgram $orientation)
    {
        $this->authorizeOrientationManager($orientation);

        $orientation->load(['plant']);
        $participants = $orientation->participants ?? [];
        $totalParticipants = count($participants);

        $data = [
            'orientation' => $orientation,
            'participants' => $participants,
            'totalParticipants' => $totalParticipants,
        ];

        $pdf = Pdf::loadView('orientation.exports.participants-pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Peserta_Orientation_' . str_replace(' ', '_', $orientation->batch_name) . '.pdf');
    }

    /**
     * Administrator can manage every program. HR PIC can manage only the
     * program where their NIK is explicitly assigned in the hr_pic JSON.
     */
    private function canManageOrientation(?OrientationProgram $orientation = null): bool
    {
        $nik = $this->currentUserNik();

        // Super Admin / Admin khusus
        if ($nik === '924330') {
            return true;
        }

        // User yang terdaftar di access orientation
        $hasOrientationAccess = UserAccessOrientation::where('nik', $nik)
            ->where('status', 'ACTIVE')
            ->exists();

        if ($hasOrientationAccess) {
            return true;
        }

        // PIC HR yang ditugaskan pada orientation tertentu
        return $orientation !== null && $this->isAssignedHrPic($orientation);
    }

    private function currentUserNik(): string
    {
        return trim((string) session('nik', ''));
    }

    private function authorizeOrientationManager(?OrientationProgram $orientation = null): void
    {
        abort_unless(
            $this->canManageOrientation($orientation),
            403,
            'Anda tidak memiliki akses untuk mengelola Orientation Program.'
        );
    }

    private function authorizeOrientationViewer(OrientationProgram $orientation): void
    {
        abort_unless(
            $this->canViewOrientation($orientation),
            403,
            'Anda hanya dapat melihat Orientation Program yang diikuti.'
        );
    }

    private function canViewOrientation(?OrientationProgram $orientation): bool
    {
        if (!$orientation) {
            return false;
        }

        if ($this->canManageOrientation($orientation)) {
            return true;
        }

        $userNik = $this->currentUserNik();
        return collect($orientation->participants ?? [])->contains(function ($participant) use ($userNik) {
            $participantNik = is_array($participant) ? ($participant['nik'] ?? '') : $participant;

            return (string) $participantNik === $userNik;
        });
    }

    private function isAssignedHrPic(OrientationProgram $orientation): bool
    {
        $userNik = $this->currentUserNik();

        return $userNik !== '' && collect($orientation->hr_pic ?? [])->contains(function ($hrPic) use ($userNik) {
            $hrPicNik = is_array($hrPic) ? ($hrPic['nik'] ?? '') : $hrPic;

            return (string) $hrPicNik === $userNik;
        });
    }


    /**
     * Simpan kehadiran + pre/post test, lalu set status kegiatan.
     */
    public function saveAttendance(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:' . $this->dbConnection . '.orientation_activities,id',
            'attendances' => 'required|array',
            'attendances.*.nik' => 'required|string',
            'attendances.*.is_present' => 'required|boolean',
            'attendances.*.pre_test' => 'nullable|numeric|min:0|max:100',
            'attendances.*.post_test' => 'nullable|numeric|min:0|max:100',
            'attendances.*.note' => 'nullable|string|max:255',
            'next_status' => 'required|in:ongoing,completed',
        ]);

        try {
            $activity = OrientationActivity::findOrFail($request->id);
            if (!$this->canManageOrientation($activity->orientation)) {
                return response()->json(['message' => 'Tidak memiliki akses.'], 403);
            }

            $data = [
                'attendances' => $request->attendances,
                'status' => $request->next_status,
            ];

            if ($request->next_status === 'ongoing' && !$activity->started_at) {
                $data['started_at'] = now();
            }

            if ($request->next_status === 'completed' && !$activity->completed_at) {
                $data['completed_at'] = now();
            }

            $activity->update($data);

            // Cek apakah semua kegiatan selesai → program completed
            $orientation = $activity->orientation;
            $total = $orientation->activities()->count();
            $completed = $orientation->activities()->where('status', 'completed')->count();

            if ($total > 0 && $total === $completed && in_array($orientation->status, ['active', 'pending'])) {
                $orientation->update(['status' => 'completed']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kehadiran berhasil disimpan.',
                'data' => [
                    'status' => $activity->status,
                    'started_at' => $activity->started_at,
                    'completed_at' => $activity->completed_at,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error save attendance: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Simpan reaksi evaluasi untuk kegiatan.
     */
    public function saveReaction(Request $request)
    {
        $request->validate([
            'orientation_activity_id' => 'required|exists:' . $this->dbConnection . '.orientation_activities,id',
            'reactions' => 'required|array|min:1',
            'reactions.*.reaction_id' => 'required|exists:' . $this->dbConnection . '.master_reaksi_evaluasi,id',
            'reactions.*.rating' => 'required|integer|min:1|max:4',
            'reactions.*.note' => 'nullable|string|max:500',
        ]);

        try {
            $activity = OrientationActivity::findOrFail($request->orientation_activity_id);
            $orientation = $activity->orientation;
            $userNik = $this->currentUserNik();

            // =====================================================
            // CEK: HANYA PESERTA yang boleh isi reaksi
            // =====================================================
            if (!$this->isParticipantOf($orientation, $userNik)) {
                return response()->json(['message' => 'Hanya peserta program yang dapat memberikan reaksi.'], 403);
            }

            if ($activity->status !== 'completed') {
                return response()->json(['message' => 'Reaksi hanya bisa diisi untuk kegiatan yang sudah selesai.'], 422);
            }

            $picData = $this->getMultiplePicData([$userNik]);
            $participantData = $picData[$userNik] ?? ['nik' => $userNik, 'nama' => $userNik];

            DB::connection($this->dbConnection)->transaction(function () use ($request, $activity, $participantData, $userNik) {
                foreach ($request->reactions as $reaction) {
                    // Ambil semua reaksi untuk (activity + reaction_id) — cari manual
                    $candidates = OrientationActivityReaction::where('orientation_activity_id', $activity->id)
                        ->where('reaction_id', $reaction['reaction_id'])
                        ->get();

                    // Cari yang NIK-nya cocok (support string & array)
                    $existing = $candidates->first(function ($r) use ($userNik) {
                        $emp = $r->employee_id;

                        // Kalau string JSON, decode dulu
                        if (is_string($emp)) {
                            $decoded = json_decode($emp, true);
                            if (is_array($decoded)) $emp = $decoded;
                        }

                        $nik = is_array($emp) ? ($emp['nik'] ?? '') : $emp;
                        return trim((string) $nik) === trim((string) $userNik);
                    });

                    if ($existing) {
                        $existing->update([
                            'rating' => $reaction['rating'],
                            'note' => $reaction['note'] ?? null,
                        ]);
                    } else {
                        OrientationActivityReaction::create([
                            'orientation_activity_id' => $activity->id,
                            'reaction_id' => $reaction['reaction_id'],
                            'employee_id' => $participantData,
                            'rating' => $reaction['rating'],
                            'note' => $reaction['note'] ?? null,
                        ]);
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Reaksi berhasil disimpan.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error save reaction: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Ambil reaksi existing untuk kegiatan (untuk prefill modal).
     */
    public function getReactions($activityId)
    {
        try {
            $activity = OrientationActivity::findOrFail($activityId);
            $orientation = $activity->orientation;
            $userNik = $this->currentUserNik();

            // =====================================================
            // AKSES:
            // - Peserta: hanya lihat reaksi MILIKNYA SENDIRI (untuk prefill)
            // - HR PIC / Admin: lihat SEMUA reaksi (hasil agregat)
            // =====================================================
            $isManager = $this->canManageOrientation($orientation);
            $isParticipant = $this->isParticipantOf($orientation, $userNik);

            if (!$isManager && !$isParticipant) {
                return response()->json(['message' => 'Tidak memiliki akses.'], 403);
            }

            // Masters
            $masters = MasterReaksiEvaluasi::where('status', 1)
                ->orderBy('reaksi_name')
                ->get(['id', 'reaksi_name']);

            if ($isManager) {
                // HR PIC / Admin → LIHAT SEMUA (agregat)
                $allReactions = OrientationActivityReaction::where('orientation_activity_id', $activityId)->get();

                // Group by reaction_id
                $grouped = $allReactions->groupBy('reaction_id')->map(function ($items) {
                    $ratings = $items->pluck('rating')->filter()->map(fn($r) => (int) $r);

                    return [
                        'count' => $items->count(),
                        'avg' => $ratings->count() > 0 ? round($ratings->avg(), 1) : null,
                        'distribution' => [
                            1 => $ratings->filter(fn($r) => $r === 1)->count(),
                            2 => $ratings->filter(fn($r) => $r === 2)->count(),
                            3 => $ratings->filter(fn($r) => $r === 3)->count(),
                            4 => $ratings->filter(fn($r) => $r === 4)->count(),
                        ],
                        'participants' => $items->map(fn($r) => [
                            'nik' => $r->employee_id['nik'] ?? '-',
                            'nama' => $r->employee_id['nama'] ?? '-',
                            'rating' => $r->rating,
                            'note' => $r->note,
                        ])->values()->all(),
                    ];
                });

                return response()->json([
                    'success' => true,
                    'mode' => 'result', // mode lihat hasil
                    'data' => [
                        'masters' => $masters,
                        'results' => $grouped,
                        'total_respondents' => $allReactions->pluck('employee_id.nik')->unique()->count(),
                    ]
                ]);
            } else {
                // Peserta → HANYA MILIKNYA (untuk prefill form)
                $myReactions = OrientationActivityReaction::where('orientation_activity_id', $activityId)
                    ->get()
                    ->filter(fn($r) => (string) ($r->employee_id['nik'] ?? '') === (string) $userNik)
                    ->keyBy('reaction_id')
                    ->map(fn($r) => [
                        'rating' => $r->rating,
                        'note' => $r->note,
                    ]);

                return response()->json([
                    'success' => true,
                    'mode' => 'input', // mode isi reaksi
                    'data' => [
                        'masters' => $masters,
                        'existing' => $myReactions,
                        'can_edit' => true,
                    ]
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function isParticipantOf(OrientationProgram $orientation, string $userNik): bool
    {
        if ($userNik === '') return false;

        return collect($orientation->participants ?? [])->contains(function ($p) use ($userNik) {
            $nik = is_array($p) ? ($p['nik'] ?? '') : $p;
            return trim((string) $nik) === trim($userNik);
        });
    }
}