<?php

namespace App\Http\Controllers\Orientation;

use App\Http\Controllers\Controller;
use App\Models\Orientation\MasterOrientationActivity;
use App\Models\Orientation\MasterPlant;
use App\Models\Orientation\OrientationActivity;
use App\Models\Orientation\OrientationProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class OrientationProgramController extends Controller
{
    /**
     * Database connection name for development/test database
     */
    protected $dbConnection = 'dev_test';

    /**
     * Daftar orientation program dari database dev_test.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;
        $search = trim((string) $request->input('search'));

        $programs = OrientationProgram::query()
            ->with(['plant', 'activities'])
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
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $allPrograms = OrientationProgram::select(['id', 'participants', 'status'])->get();

        $statistics = [
            'total_programs' => $allPrograms->count(),
            'total_participants' => $allPrograms->sum(fn(OrientationProgram $program) => count($program->participants ?? [])),
            'active_programs' => $allPrograms->where('status', 'active')->count(),
            'completed_programs' => $allPrograms->where('status', 'completed')->count(),
        ];

        return view('orientation.index', compact('programs', 'statistics', 'search', 'perPage'));
    }

    /**
     * Form Create Orientation
     */
    public function create()
    {
        $plants = MasterPlant::orderBy('name_plant', 'asc')->get();
        $masterOrientationActivities = MasterOrientationActivity::orderBy('activity_name', 'asc')->get();

        return view('orientation.create-orientation', compact(
            'plants',
            'masterOrientationActivities'
        ));
    }

    /**
     * Form edit orientation. Plant tidak dapat diubah setelah program dibuat.
     */
    public function edit(OrientationProgram $orientation)
    {
        $orientation->load(['plant', 'activities.masterActivity']);

        // Ambil semua NIK PIC dari kegiatan untuk diambil datanya sekaligus
        $niks = $orientation->activities->pluck('pic_employee_id')->filter()->unique()->values()->all();
        $allPicData = $this->getMultiplePicData($niks);

        $masterOrientationActivities = MasterOrientationActivity::orderBy('activity_name', 'asc')->get();
        $initialParticipants = $orientation->participants ?? [];

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
        ));
    }

    /**
     * Perbarui peserta dan rincian kegiatan tanpa mengubah plant program.
     */
    public function update(Request $request, OrientationProgram $orientation)
    {
        $request->merge(['participants' => $this->normalizeParticipants($request->input('participants', []))]);

        $request->validate([
            'participants' => 'required|array|min:1',
            'participants.*.nik' => 'required|string|max:100',
            'participants.*.nama' => 'required|string|max:255',
            'participants.*.jabatan' => 'nullable|string|max:255',
            'participants.*.dept' => 'nullable|string|max:255',
            'activities' => 'required|array|min:1',
            'activities.*.activity_id' => 'required|exists:dev_test.master_orientation_activities,id',
            'activities.*.tanggal' => 'required|date',
            'activities.*.waktu_mulai' => 'required',
            'activities.*.waktu_selesai' => 'required',
            'activities.*.pic' => 'required|string|max:255',
            'activities.*.pic_nik' => 'required|string|max:100',
        ]);

        $activityIds = collect($request->input('activities'))->pluck('activity_id')->unique();
        $allowedActivityIds = MasterOrientationActivity::whereIn('id', $activityIds)
            ->where('status', true)
            ->get()
            ->filter(fn(MasterOrientationActivity $activity) => in_array(
                (string) $orientation->master_plants_id,
                array_map('strval', $activity->plants ?? []),
                true
            ))
            ->pluck('id');

        if ($allowedActivityIds->count() !== $activityIds->count()) {
            return response()->json(['message' => 'Terdapat kegiatan yang tidak tersedia untuk plant program ini.'], 422);
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
        $request->merge(['participants' => $this->normalizeParticipants($request->input('participants', []))]);

        $request->validate([
            'plant_id' => 'required|exists:dev_test.master_plants,id',
            'participants' => 'required|array|min:1',
            'participants.*.nik' => 'required|string|max:100',
            'participants.*.nama' => 'required|string|max:255',
            'participants.*.jabatan' => 'nullable|string|max:255',
            'participants.*.dept' => 'nullable|string|max:255',
            'activities' => 'required|array|min:1',
            'activities.*.activity_id' => 'required|exists:dev_test.master_orientation_activities,id',
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
            ->get()
            ->filter(fn(MasterOrientationActivity $activity) => in_array(
                (string) $request->plant_id,
                array_map('strval', $activity->plants ?? []),
                true
            ))
            ->pluck('id');

        if ($allowedActivityIds->count() !== $activityIds->count()) {
            return response()->json([
                'message' => 'Terdapat kegiatan yang tidak tersedia untuk plant yang dipilih.',
            ], 422);
        }

        $orientation = DB::connection($this->dbConnection)->transaction(function () use ($request) {
            // ================================================================
            // HITUNG NOMOR URUT BATCH MENGGUNAKAN COUNT
            // ================================================================
            // Hitung total program yang sudah ada
            $totalPrograms = OrientationProgram::count();
            $nextBatchNumber = $totalPrograms + 1;
            $batchName = 'Kobin Orientation Program Batch ' . $nextBatchNumber;
            // ================================================================

            $orientation = OrientationProgram::create([
                'batch_name' => $batchName,
                'master_plants_id' => $request->plant_id,
                'participants' => array_values($request->participants),
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
     * Hapus program orientation beserta seluruh kegiatan terkait.
     */
    public function destroy(OrientationProgram $orientation)
    {
        DB::connection($this->dbConnection)->transaction(function () use ($orientation) {
            $orientation->activities()->delete();
            $orientation->delete();
        });

        return redirect()
            ->route('orientation.index')
            ->with('deleted_success', 'Orientation program berhasil dihapus.');
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
        $orientation->load([
            'plant',
            'activities.masterActivity'
        ]);

        // Ambil semua NIK PIC dari kegiatan untuk diambil datanya sekaligus
        $niks = $orientation->activities->pluck('pic_employee_id')->filter()->unique()->values()->all();
        $allPicData = $this->getMultiplePicData($niks);

        // Ambil data kegiatan dengan informasi lengkap
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

        // Ambil data peserta dari JSON field di OrientationProgram
        $participantsFromDb = $orientation->participants ?? [];
        $totalParticipants = count($participantsFromDb);

        // Ambil semua NIK peserta untuk mendapatkan data lengkap
        $participantNiks = collect($participantsFromDb)->pluck('nik')->filter()->values()->all();
        $allParticipantData = $this->getMultiplePicData($participantNiks);

        // Enrich data peserta dengan informasi lengkap dan progress - ubah menjadi Collection
        $participantDetails = collect($participantsFromDb)->map(function ($participant) use ($allParticipantData, $orientation) {
            $nik = $participant['nik'] ?? null;
            $data = $nik ? ($allParticipantData[$nik] ?? null) : null;

            // Hitung progress peserta berdasarkan activities yang sudah completed
            $participantActivities = $orientation->activities
                ->where('pic_employee_id', $nik);

            $totalActivities = $participantActivities->count();
            $completedActivities = $participantActivities->where('status', 'completed')->count();
            $progress = $totalActivities > 0 ? round(($completedActivities / $totalActivities) * 100) : 0;

            // Tentukan status berdasarkan progress
            $status = 'pending';
            if ($progress == 100 && $totalActivities > 0) {
                $status = 'completed';
            } elseif ($progress > 0) {
                $status = 'active';
            }

            return [
                'nik' => $nik,
                'nama' => $data['nama'] ?? ($participant['nama'] ?? $nik),
                'jabatan' => $data['jabatan'] ?? ($participant['jabatan'] ?? '-'),
                'dept' => $data['dept'] ?? ($participant['dept'] ?? '-'),
                'email' => $data['email'] ?? ($participant['email'] ?? '-'),
                'status' => $status,
                'progress' => $progress,
                'total_activities' => $totalActivities,
                'completed_activities' => $completedActivities,
                'join_date' => $participant['join_date'] ?? null,
            ];
        });

        // Statistik peserta - gunakan Collection methods
        $participantStats = [
            'total' => $participantDetails->count(),
            'completed' => $participantDetails->where('status', 'completed')->count(),
            'active' => $participantDetails->where('status', 'active')->count(),
            'pending' => $participantDetails->where('status', 'pending')->count(),
        ];

        // Hitung statistik kegiatan
        $totalActivities = $orientation->activities->count();
        $completedActivities = $orientation->activities->where('status', 'completed')->count();
        $pendingActivities = $orientation->activities->where('status', 'pending')->count();
        $inProgressActivities = $orientation->activities->where('status', 'in_progress')->count();

        // Kelompokkan kegiatan berdasarkan tanggal
        $activitiesByDate = $orientation->activities
            ->groupBy(function ($activity) {
                return $activity->activity_date?->format('Y-m-d') ?? 'no-date';
            })
            ->map(function ($activities, $date) {
                return [
                    'date' => $date,
                    'date_formatted' => $date !== 'no-date' ? date('d M Y', strtotime($date)) : 'Tanggal tidak tersedia',
                    'activities' => $activities->map(function ($activity) {
                        return [
                            'id' => $activity->id,
                            'name' => $activity->masterActivity?->activity_name ?? 'Kegiatan',
                            'start_time' => $activity->start_time?->format('H:i') ?? '-',
                            'end_time' => $activity->end_time?->format('H:i') ?? '-',
                            'pic' => $activity->pic_employee_id,
                            'status' => $activity->status ?? 'pending',
                        ];
                    })->values()->all()
                ];
            })->values()->all();

        // Ambil semua master activities yang tersedia untuk plant ini
        $availableActivities = MasterOrientationActivity::where('status', true)
            ->where(function ($query) use ($orientation) {
                $query->whereJsonContains('plants', (string) $orientation->master_plants_id)
                    ->orWhereNull('plants')
                    ->orWhere('plants', '[]');
            })
            ->orderBy('activity_name', 'asc')
            ->get();

        // Statistik program
        $programStats = [
            'total_activities' => $totalActivities,
            'completed_activities' => $completedActivities,
            'pending_activities' => $pendingActivities,
            'in_progress_activities' => $inProgressActivities,
            'total_participants' => $totalParticipants,
            'completion_percentage' => $totalActivities > 0
                ? round(($completedActivities / $totalActivities) * 100)
                : 0,
        ];

        // Cek apakah ada konflik jadwal
        $scheduleConflicts = $this->checkScheduleConflicts($orientation->activities);

        // Kembalikan view dengan array asosiatif
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
            'scheduleConflicts' => $scheduleConflicts
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
                'id' => 'required|exists:dev_test.orientation_activities,id',
                'score' => 'required|numeric|min:0|max:100',
                'score_note' => 'nullable|string|max:255',
            ]);

            $activity = OrientationActivity::find($request->id);
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
                'id' => 'required|exists:dev_test.orientation_activities,id',
                'status' => 'required|in:pending,ongoing,completed,cancelled',
            ]);

            $activity = OrientationActivity::find($request->id);

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
                'id' => 'required|exists:dev_test.orientation_activities,id'
            ]);

            $activity = OrientationActivity::find($request->id);

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
            'totalParticipants' => $totalParticipants,
            'plantName' => $orientation->plant->name_plant ?? '-',
            'batchNumber' => $batchNumber,
            'period' => $period,
        ];

        $pdf = Pdf::loadView('orientation.exports.schedule-pdf', $data);
        $pdf->setPaper('A4', 'landscape');
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
}