<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDailyActivityRequest;
use App\Models\DailyActivity;
use App\Models\Depaterment;
use App\Models\P1;
use App\Models\P2;
use App\Models\Project;
use App\Models\SubProject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DailyActivityController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::select('id', 'name')->get();
        $departements = Depaterment::select('id', 'name')->get();
        $subProjects = SubProject::select('id', 'nama_pt', 'nama_sub_project', 'project_id')->get();

        $query = DailyActivity::with(['user.departement', 'subProject.project'])
            ->where('user_id', auth()->id())
            ->orderBy('tanggal', 'desc');

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $totalWaktu = $query->sum('waktu');
        $dailyActivities = $query->paginate(10);

        return view('users.index', compact('projects', 'departements', 'subProjects', 'dailyActivities', 'totalWaktu'));
    }


    public function store(StoreDailyActivityRequest $request)
    {
        if ($request->nama_sub_project === '-') {
            // Ambil subproject dengan nama_pt dan nama_sub_project NULL
            $subproject = SubProject::where('nama_pt', $request->nama_pt)
                ->whereNull('nama_sub_project')
                ->first();
        } else {
            $subproject = SubProject::where('nama_pt', $request->nama_pt)
                ->where('nama_sub_project', $request->nama_sub_project)
                ->first();
        }

        if (!$subproject) {
            return redirect()->back()->with('error', 'Sub Project tidak ditemukan');
        }

        DailyActivity::create([
            'user_id' => Auth::id(),
            'sub_project_id' => $subproject->id, // dipastikan tidak null
            'tanggal' => $request->tanggal,
            'waktu' => $request->waktu,
            'keterangan' => $request->keterangan,
            'cuti' => $request->cuti ?? 0,
            'ijin' => $request->ijin ?? 0,
            'sakit' => $request->sakit ?? 0,
        ]);

        return redirect()->back()->with('success', 'Daily Activity berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pt' => 'required|string',
            'nama_sub_project' => 'nullable|string',
            'tanggal' => 'required|date',
            'waktu' => [
                'required',
                'numeric',
                'max:8',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value == 0 && $request->jenis_izin === null) {
                        $fail('Waktu tidak boleh 0 jika tidak ada jenis izin.');
                    }
                }
            ],
            'keterangan' => 'required|string',
            'jenis_izin' => 'nullable|in:cuti,ijin,sakit'
        ]);

        $namaSubProject = $request->nama_sub_project === '-' ? null : $request->nama_sub_project;

        $subProject = SubProject::where('nama_pt', $request->nama_pt)
            ->where(function ($query) use ($namaSubProject) {
                if (is_null($namaSubProject)) {
                    $query->whereNull('nama_sub_project');
                } else {
                    $query->where('nama_sub_project', $namaSubProject);
                }
            })
            ->first();

        if (!$subProject) {
            return back()->withErrors(['nama_sub_project' => 'Sub project tidak ditemukan.']);
        }

        // Reset semua izin
        $izin = [
            'cuti' => 0,
            'ijin' => 0,
            'sakit' => 0
        ];

        // Validasi agar hanya satu jenis izin aktif
        if ($request->jenis_izin && array_key_exists($request->jenis_izin, $izin)) {
            $izin[$request->jenis_izin] = 1;
        }

        $activity = DailyActivity::findOrFail($id);
        $activity->update([
            'sub_project_id' => $subProject?->id,
            'tanggal' => $request->tanggal,
            'waktu' => $request->waktu,
            'keterangan' => $request->keterangan,
            'cuti' => $izin['cuti'],
            'ijin' => $izin['ijin'],
            'sakit' => $izin['sakit'],
        ]);

        return redirect()->back()->with('success', 'Aktivitas berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $activity = DailyActivity::findOrFail($id);
        $activity->delete();

        return redirect()->back()->with('success', 'Aktivitas berhasil dihapus!');
    }

    public function getSubProjects(Request $request)
    {
        $subprojects = SubProject::select('nama_pt', 'nama_sub_project')->get();
        return response()->json($subprojects);
    }

    function getWeekOfMonth($date)
    {
        $carbonDate = Carbon::parse($date);
        $firstDay = $carbonDate->copy()->startOfMonth();
        $weekOfMonth = intval(floor(($carbonDate->day - 1 + $firstDay->dayOfWeek) / 7)) + 1;
        return 'w' . $weekOfMonth;
    }

    public function monitoring(Request $request)
    {
        $user = auth()->user();

        $month = $request->input('month', now()->format('m'));
        $year = $request->input('year', now()->format('Y'));

        $dailyActivities = DailyActivity::with(['user.departement', 'subProject.project'])
            ->where('user_id', $user->id)
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->get();

        // dd($dailyActivities);

        $userSubProjectIds = $dailyActivities->pluck('sub_project_id')->unique();

        $subprojects = SubProject::with('project')->whereIn('id', $userSubProjectIds)->get();

        $projectIds = $subprojects->pluck('project_id')->unique();

        $projects = Project::whereIn('id', $projectIds)->get();

        $users = collect([$user->load('departement')]);

        $groupedActivities = [];

        foreach ($dailyActivities as $activity) {
            $week = $this->getWeekOfMonth($activity->tanggal);
            $subprojectId = $activity->sub_project_id;
            $userId = $activity->user_id;
            $projectId = $activity->subProject->project_id;

            $groupedActivities[$userId][$projectId][$subprojectId][$week] =
                ($groupedActivities[$userId][$projectId][$subprojectId][$week] ?? 0) + $activity->waktu;
        }

        $detailActivities = [];

        foreach ($dailyActivities as $activity) {
            $userId = $activity->user_id;
            $projectId = $activity->subProject->project_id;
            $subProjectId = $activity->sub_project_id;
            $week = 'w' . $this->getWeekOfMonth($activity->tanggal);

            $detailActivities[$userId][$projectId][$subProjectId][$week][] = [
                'tanggal' => \Carbon\Carbon::parse($activity->tanggal)->format('d/m/Y'),
                'keterangan' => $activity->keterangan,
                'jam' => $activity->waktu,
                'nama_pt' => $activity->subProject->nama_pt ?? '',
                'nama_sub_project' => $activity->subProject->nama_sub_project ?? '',
                'week' => substr($week, 1),
            ];
        }

        return view('users.monitoring.index', compact(
            'projects',
            'subprojects',
            'users',
            'dailyActivities',
            'groupedActivities',
            'detailActivities'
        ));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        $data = Excel::toArray([], $request->file('file'));
        $rows = $data[0]; // ambil sheet pertama
        $header = array_map('strtolower', $rows[0]);
        unset($rows[0]); // buang header

        $insertData = [];

        // Fungsi bantu untuk normalisasi teks
        $normalizeSpaces = function ($text) {
            return preg_replace('/\s+/', ' ', strtoupper(trim($text)));
        };

        foreach ($rows as $row) {
            $mapped = array_combine($header, $row);

            $namaPt = $normalizeSpaces($mapped['nama pt'] ?? '');
            $namaProject = $normalizeSpaces($mapped['nama project'] ?? '');

            if (is_numeric($mapped['tanggal'])) {
                $tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($mapped['tanggal'])->format('Y-m-d');
            } else {
                $tanggal = \Carbon\Carbon::parse($mapped['tanggal'])->format('Y-m-d');
            }

            $waktu = $mapped['waktu'];
            $tipe = strtolower(trim($mapped['tipe'] ?? ''));
            $keterangan = $mapped['keterangan'] ?? '';

            if (!is_numeric($waktu) || $waktu < 0 || $waktu > 8) {
                return back()->with('error', 'Format waktu salah. Hanya boleh angka antara 0 hingga 8.');
            }

            $query = SubProject::where('nama_pt', $namaPt);
            if (empty($namaProject)) {
                $query->whereNull('nama_sub_project');
            } else {
                $query->where('nama_sub_project', $namaProject);
            }
            $subProject = $query->first();

            if (!$subProject) {
                return back()->with('error', "Sub project tidak ditemukan: $namaPt - $namaProject");
            }

            $cuti = $sakit = $ijin = 0;
            switch ($tipe) {
                case 'sakit':
                    $sakit = 1;
                    break;
                case 'cuti':
                    $cuti = 1;
                    break;
                case 'ijin':
                    $ijin = 1;
                    break;
            }

            $insertData[] = [
                'user_id' => Auth::id(),
                'sub_project_id' => $subProject->id,
                'tanggal' => $tanggal,
                'waktu' => $waktu,
                'keterangan' => $keterangan,
                'cuti' => $cuti,
                'sakit' => $sakit,
                'ijin' => $ijin,
            ];
        }

        DailyActivity::insert($insertData);

        return back()->with('success', 'Data berhasil diimport!');
    }
}
