<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Orientation\OrientationProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ParticipantOrientationController extends Controller
{
    /**
     * Database connection name
     */
    protected $dbConnection = 'dev_test';

    /**
     * Get list of orientation programs for a participant
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getParticipantOrientations(Request $request)
    {
        try {
            $request->validate([
                'nik' => 'required|string|max:100',
            ]);

            $nik = $request->input('nik');

            // Cari semua program orientation yang mengandung NIK peserta
            $programs = OrientationProgram::with(['plant', 'activities.masterActivity'])
                ->where('status', '!=', 'cancelled')
                ->get()
                ->filter(function ($program) use ($nik) {
                    $participants = $program->participants ?? [];
                    return collect($participants)->contains('nik', $nik);
                })
                ->values();

            if ($programs->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada orientation program untuk peserta ini.',
                    'data' => [],
                ]);
            }

            // Format data
            $formattedPrograms = $programs->map(function ($program) use ($nik) {
                // Ambil data peserta dari program
                $participantData = collect($program->participants ?? [])
                    ->firstWhere('nik', $nik);

                // Hitung progress peserta
                $totalActivities = $program->activities->count();
                $completedActivities = $program->activities
                    ->where('status', 'completed')
                    ->count();
                
                $progress = $totalActivities > 0 
                    ? round(($completedActivities / $totalActivities) * 100) 
                    : 0;

                // Tentukan status peserta
                $participantStatus = 'pending';
                if ($progress == 100 && $totalActivities > 0) {
                    $participantStatus = 'completed';
                } elseif ($progress > 0) {
                    $participantStatus = 'active';
                }

                return [
                    'id' => $program->id,
                    'batch_name' => $program->batch_name,
                    'plant' => [
                        'id' => $program->plant?->id,
                        'name' => $program->plant?->name_plant,
                        'code' => $program->plant?->code,
                    ],
                    'participant' => [
                        'nik' => $nik,
                        'nama' => $participantData['nama'] ?? $nik,
                        'jabatan' => $participantData['jabatan'] ?? null,
                        'dept' => $participantData['dept'] ?? null,
                    ],
                    'status' => $participantStatus,
                    'progress' => $progress,
                    'total_activities' => $totalActivities,
                    'completed_activities' => $completedActivities,
                    'start_date' => $program->activities->min('activity_date')?->format('Y-m-d'),
                    'end_date' => $program->activities->max('activity_date')?->format('Y-m-d'),
                    'created_at' => $program->created_at?->format('Y-m-d H:i:s'),
                    'updated_at' => $program->updated_at?->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data orientation program berhasil diambil.',
                'data' => $formattedPrograms,
            ]);

        } catch (\Exception $e) {
            Log::error('Error get participant orientations: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data orientation program: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Get detail of a specific orientation program including all activities
     * 
     * @param Request $request
     * @param int $programId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getParticipantOrientationDetail(Request $request, $programId)
    {
        try {
            $request->validate([
                'nik' => 'required|string|max:100',
            ]);

            $nik = $request->input('nik');

            // Cari program dengan ID tertentu
            $program = OrientationProgram::with(['plant', 'activities.masterActivity'])
                ->where('id', $programId)
                ->where('status', '!=', 'cancelled')
                ->first();

            if (!$program) {
                return response()->json([
                    'success' => false,
                    'message' => 'Orientation program tidak ditemukan.',
                    'data' => null,
                ], 404);
            }

            // Cek apakah NIK terdaftar sebagai peserta
            $participants = $program->participants ?? [];
            $participantData = collect($participants)->firstWhere('nik', $nik);

            if (!$participantData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak terdaftar dalam program ini.',
                    'data' => null,
                ], 403);
            }

            // Ambil semua NIK PIC dari kegiatan untuk data lengkap
            $niks = $program->activities->pluck('pic_employee_id')->filter()->unique()->values()->all();
            $allPicData = $this->getMultiplePicData($niks);

            // Format activities
            $activities = $program->activities
                ->sortBy('activity_date')
                ->values()
                ->map(function ($activity) use ($allPicData) {
                    $picNik = $activity->pic_employee_id;
                    $picData = $allPicData[$picNik] ?? null;

                    return [
                        'id' => $activity->id,
                        'activity_name' => $activity->masterActivity?->activity_name ?? 'Kegiatan',
                        'description' => $activity->masterActivity?->description ?? '',
                        'activity_date' => $activity->activity_date?->format('Y-m-d'),
                        'activity_date_formatted' => $activity->activity_date?->format('d F Y'),
                        'start_time' => $activity->start_time?->format('H:i'),
                        'end_time' => $activity->end_time?->format('H:i'),
                        'time_range' => ($activity->start_time?->format('H:i') ?? '-') . ' - ' . ($activity->end_time?->format('H:i') ?? '-'),
                        'pic' => [
                            'nik' => $picNik,
                            'nama' => $picData['nama'] ?? $picNik,
                            'jabatan' => $picData['jabatan'] ?? '-',
                            'dept' => $picData['dept'] ?? '-',
                        ],
                        'status' => $activity->status ?? 'pending',
                        'status_label' => $this->getStatusLabel($activity->status ?? 'pending'),
                        'status_color' => $this->getStatusColor($activity->status ?? 'pending'),
                        'score' => $activity->score,
                        'score_note' => $activity->score_note,
                        'started_at' => $activity->started_at?->format('Y-m-d H:i:s'),
                        'completed_at' => $activity->completed_at?->format('Y-m-d H:i:s'),
                    ];
                });

            // Hitung statistik peserta
            $totalActivities = $program->activities->count();
            $completedActivities = $program->activities->where('status', 'completed')->count();
            $progress = $totalActivities > 0 ? round(($completedActivities / $totalActivities) * 100) : 0;

            $participantStatus = 'pending';
            if ($progress == 100 && $totalActivities > 0) {
                $participantStatus = 'completed';
            } elseif ($progress > 0) {
                $participantStatus = 'active';
            }

            // Statistik program
            $programStats = [
                'total_activities' => $totalActivities,
                'completed_activities' => $completedActivities,
                'pending_activities' => $program->activities->where('status', 'pending')->count(),
                'in_progress_activities' => $program->activities->where('status', 'in_progress')->count(),
                'total_participants' => count($participants),
                'completion_percentage' => $totalActivities > 0 
                    ? round(($completedActivities / $totalActivities) * 100) 
                    : 0,
            ];

            // Kelompokkan activities berdasarkan tanggal
            $activitiesByDate = $program->activities
                ->groupBy(function ($activity) {
                    return $activity->activity_date?->format('Y-m-d') ?? 'no-date';
                })
                ->map(function ($activities, $date) use ($allPicData) {
                    return [
                        'date' => $date,
                        'date_formatted' => $date !== 'no-date' 
                            ? date('l, d F Y', strtotime($date)) 
                            : 'Tanggal tidak tersedia',
                        'activities' => $activities->sortBy('start_time')->map(function ($activity) use ($allPicData) {
                            $picNik = $activity->pic_employee_id;
                            $picData = $allPicData[$picNik] ?? null;

                            return [
                                'id' => $activity->id,
                                'name' => $activity->masterActivity?->activity_name ?? 'Kegiatan',
                                'start_time' => $activity->start_time?->format('H:i'),
                                'end_time' => $activity->end_time?->format('H:i'),
                                'time_range' => ($activity->start_time?->format('H:i') ?? '-') . ' - ' . ($activity->end_time?->format('H:i') ?? '-'),
                                'pic' => [
                                    'nik' => $picNik,
                                    'nama' => $picData['nama'] ?? $picNik,
                                ],
                                'status' => $activity->status ?? 'pending',
                                'status_label' => $this->getStatusLabel($activity->status ?? 'pending'),
                            ];
                        })->values()->all(),
                    ];
                })->values()->all();

            // Format response
            $data = [
                'program' => [
                    'id' => $program->id,
                    'batch_name' => $program->batch_name,
                    'status' => $program->status,
                    'status_label' => $this->getProgramStatusLabel($program->status),
                ],
                'plant' => [
                    'id' => $program->plant?->id,
                    'name' => $program->plant?->name_plant,
                    'code' => $program->plant?->code,
                    'address' => $program->plant?->address,
                ],
                'participant' => [
                    'nik' => $nik,
                    'nama' => $participantData['nama'] ?? $nik,
                    'jabatan' => $participantData['jabatan'] ?? null,
                    'dept' => $participantData['dept'] ?? null,
                    'status' => $participantStatus,
                    'progress' => $progress,
                ],
                'statistics' => $programStats,
                'activities' => $activities,
                'activities_by_date' => $activitiesByDate,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Detail orientation program berhasil diambil.',
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            Log::error('Error get participant orientation detail: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail orientation program: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Get activities for a specific orientation program (simplified version)
     * 
     * @param Request $request
     * @param int $programId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getParticipantActivities(Request $request, $programId)
    {
        try {
            $request->validate([
                'nik' => 'required|string|max:100',
            ]);

            $nik = $request->input('nik');

            // Cek program dan keikutsertaan peserta
            $program = OrientationProgram::with(['activities.masterActivity'])
                ->where('id', $programId)
                ->where('status', '!=', 'cancelled')
                ->first();

            if (!$program) {
                return response()->json([
                    'success' => false,
                    'message' => 'Orientation program tidak ditemukan.',
                    'data' => null,
                ], 404);
            }

            $participants = $program->participants ?? [];
            $participantData = collect($participants)->firstWhere('nik', $nik);

            if (!$participantData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak terdaftar dalam program ini.',
                    'data' => null,
                ], 403);
            }

            // Ambil data PIC
            $niks = $program->activities->pluck('pic_employee_id')->filter()->unique()->values()->all();
            $allPicData = $this->getMultiplePicData($niks);

            // Format activities
            $activities = $program->activities
                ->sortBy('activity_date')
                ->values()
                ->map(function ($activity) use ($allPicData) {
                    $picNik = $activity->pic_employee_id;
                    $picData = $allPicData[$picNik] ?? null;

                    return [
                        'id' => $activity->id,
                        'activity_name' => $activity->masterActivity?->activity_name ?? 'Kegiatan',
                        'description' => $activity->masterActivity?->description ?? '',
                        'activity_date' => $activity->activity_date?->format('Y-m-d'),
                        'activity_date_formatted' => $activity->activity_date?->format('d F Y'),
                        'start_time' => $activity->start_time?->format('H:i'),
                        'end_time' => $activity->end_time?->format('H:i'),
                        'time_range' => ($activity->start_time?->format('H:i') ?? '-') . ' - ' . ($activity->end_time?->format('H:i') ?? '-'),
                        'pic_name' => $picData['nama'] ?? $picNik,
                        'status' => $activity->status ?? 'pending',
                        'status_label' => $this->getStatusLabel($activity->status ?? 'pending'),
                        'status_color' => $this->getStatusColor($activity->status ?? 'pending'),
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Data kegiatan orientation program berhasil diambil.',
                'data' => [
                    'program_id' => $program->id,
                    'batch_name' => $program->batch_name,
                    'participant_nik' => $nik,
                    'participant_name' => $participantData['nama'] ?? $nik,
                    'total_activities' => $activities->count(),
                    'activities' => $activities,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error get participant activities: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kegiatan: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Get program summary/dashboard for a participant
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getParticipantDashboard(Request $request)
    {
        try {
            $request->validate([
                'nik' => 'required|string|max:100',
            ]);

            $nik = $request->input('nik');

            // Cari semua program yang diikuti peserta
            $allPrograms = OrientationProgram::with(['plant', 'activities'])
                ->where('status', '!=', 'cancelled')
                ->get()
                ->filter(function ($program) use ($nik) {
                    $participants = $program->participants ?? [];
                    return collect($participants)->contains('nik', $nik);
                })
                ->values();

            // Statistik keseluruhan
            $totalPrograms = $allPrograms->count();
            $totalActivities = 0;
            $completedActivities = 0;
            $pendingActivities = 0;
            $ongoingPrograms = 0;
            $completedPrograms = 0;

            $programSummaries = $allPrograms->map(function ($program) use ($nik) {
                $totalAct = $program->activities->count();
                $completedAct = $program->activities->where('status', 'completed')->count();
                $progress = $totalAct > 0 ? round(($completedAct / $totalAct) * 100) : 0;

                // Ambil data peserta
                $participantData = collect($program->participants ?? [])
                    ->firstWhere('nik', $nik);

                return [
                    'id' => $program->id,
                    'batch_name' => $program->batch_name,
                    'plant_name' => $program->plant?->name_plant ?? '-',
                    'status' => $this->getProgramStatusLabel($program->status),
                    'participant_status' => $this->getParticipantStatus($progress, $totalAct),
                    'progress' => $progress,
                    'total_activities' => $totalAct,
                    'completed_activities' => $completedAct,
                    'start_date' => $program->activities->min('activity_date')?->format('d M Y'),
                    'end_date' => $program->activities->max('activity_date')?->format('d M Y'),
                ];
            });

            // Hitung statistik keseluruhan
            foreach ($allPrograms as $program) {
                $totalAct = $program->activities->count();
                $completedAct = $program->activities->where('status', 'completed')->count();
                $progress = $totalAct > 0 ? round(($completedAct / $totalAct) * 100) : 0;

                $totalActivities += $totalAct;
                $completedActivities += $completedAct;
                $pendingActivities += ($totalAct - $completedAct);

                if ($progress == 100 && $totalAct > 0) {
                    $completedPrograms++;
                } elseif ($progress > 0) {
                    $ongoingPrograms++;
                }
            }

            // Format response
            $data = [
                'participant' => [
                    'nik' => $nik,
                    'nama' => $this->getParticipantName($nik, $allPrograms),
                ],
                'statistics' => [
                    'total_programs' => $totalPrograms,
                    'total_activities' => $totalActivities,
                    'completed_activities' => $completedActivities,
                    'pending_activities' => $pendingActivities,
                    'ongoing_programs' => $ongoingPrograms,
                    'completed_programs' => $completedPrograms,
                    'overall_progress' => $totalActivities > 0 
                        ? round(($completedActivities / $totalActivities) * 100) 
                        : 0,
                ],
                'programs' => $programSummaries,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Data dashboard berhasil diambil.',
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            Log::error('Error get participant dashboard: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dashboard: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Get multiple PIC data from API with caching
     */
    private function getMultiplePicData(array $niks): array
    {
        if (empty($niks)) {
            return [];
        }

        $result = [];
        $needToFetch = [];

        // Cek cache
        foreach ($niks as $nik) {
            $cacheKey = 'pic_data_' . $nik;
            if (Cache::has($cacheKey)) {
                $result[$nik] = Cache::get($cacheKey);
            } else {
                $needToFetch[] = $nik;
            }
        }

        if (empty($needToFetch)) {
            return $result;
        }

        try {
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

                            Cache::put('pic_data_' . $nik, $picData, 3600);
                            $result[$nik] = $picData;
                        } else {
                            $picData = [
                                'nik' => $nik,
                                'nama' => $nik,
                                'jabatan' => '-',
                                'dept' => '-',
                                'plant' => '',
                                'email' => '',
                                'divisi' => '',
                            ];

                            Cache::put('pic_data_' . $nik, $picData, 300);
                            $result[$nik] = $picData;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data PIC: ' . $e->getMessage());

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
     * Get participant name from any program
     */
    private function getParticipantName(string $nik, $programs): string
    {
        foreach ($programs as $program) {
            $participants = $program->participants ?? [];
            $participant = collect($participants)->firstWhere('nik', $nik);
            if ($participant && isset($participant['nama'])) {
                return $participant['nama'];
            }
        }
        return $nik;
    }

    /**
     * Get participant status based on progress
     */
    private function getParticipantStatus(int $progress, int $totalActivities): string
    {
        if ($totalActivities === 0) {
            return 'pending';
        }
        if ($progress === 100) {
            return 'completed';
        }
        if ($progress > 0) {
            return 'ongoing';
        }
        return 'pending';
    }

    /**
     * Get status label for program
     */
    private function getProgramStatusLabel(?string $status): string
    {
        $labels = [
            'pending' => 'Menunggu',
            'active' => 'Aktif',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        return $labels[$status] ?? ucfirst($status ?? 'unknown');
    }

    /**
     * Get status label for activity
     */
    private function getStatusLabel(string $status): string
    {
        $labels = [
            'pending' => 'Menunggu',
            'in_progress' => 'Berlangsung',
            'ongoing' => 'Berlangsung',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        return $labels[$status] ?? ucfirst($status);
    }

    /**
     * Get status color for activity
     */
    private function getStatusColor(string $status): string
    {
        $colors = [
            'pending' => 'yellow',
            'in_progress' => 'blue',
            'ongoing' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red',
        ];

        return $colors[$status] ?? 'gray';
    }
}