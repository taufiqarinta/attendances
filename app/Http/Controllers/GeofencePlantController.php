<?php

namespace App\Http\Controllers;

use App\Models\GeofencePlant;
use App\Http\Requests\GeofencePlantRequest;
use Illuminate\Http\Request;

class GeofencePlantController extends Controller
{
    /**
     * Tampilkan daftar semua data geofence plant.
     */
    public function index(Request $request)
    {
        $query = GeofencePlant::query();

        // Pencarian sederhana berdasarkan plant / plant_name
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('plant', 'like', "%{$search}%")
                  ->orWhere('plant_name', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan type (IN/OUT) jika dipilih
        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        $geofencePlants = $query->orderBy('plant_name')
            ->orderBy('type')
            ->paginate(15)
            ->withQueryString();

        return view('geofence-plant.index', [
            'geofencePlants' => $geofencePlants,
            'search' => $search ?? '',
            'typeFilter' => $type ?? '',
            'types' => GeofencePlant::TYPES,
        ]);
    }

    /**
     * Tampilkan form tambah data baru.
     */
    public function create()
    {
        return view('geofence-plant.create', [
            'geofencePlant' => new GeofencePlant(),
            'types' => GeofencePlant::TYPES,
        ]);
    }

    /**
     * Simpan data baru ke database.
     */
    public function store(GeofencePlantRequest $request)
    {
        $data = $request->validated();
        $data['exclude_department'] = $this->arrayToCommaString($data['exclude_department'] ?? []);
        $data['exclude_nik'] = $this->arrayToCommaString($data['exclude_nik'] ?? []);

        GeofencePlant::create($data);

        return redirect()
            ->route('geofence-plant.index')
            ->with('success', 'Data geofence plant berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail satu data (opsional, jika dibutuhkan halaman show).
     */
    public function show(GeofencePlant $geofence_plant)
    {
        return view('geofence-plant.show', [
            'geofencePlant' => $geofence_plant,
        ]);
    }

    /**
     * Tampilkan form edit data.
     */
    public function edit(GeofencePlant $geofence_plant)
    {
        return view('geofence-plant.edit', [
            'geofencePlant' => $geofence_plant,
            'types' => GeofencePlant::TYPES,
        ]);
    }

    /**
     * Update data yang sudah ada.
     */
    public function update(GeofencePlantRequest $request, GeofencePlant $geofence_plant)
    {
        $data = $request->validated();
        $data['exclude_department'] = $this->arrayToCommaString($data['exclude_department'] ?? []);
        $data['exclude_nik'] = $this->arrayToCommaString($data['exclude_nik'] ?? []);

        $geofence_plant->update($data);

        return redirect()
            ->route('geofence-plant.index')
            ->with('success', 'Data geofence plant berhasil diperbarui.');
    }

    /**
     * Hapus data.
     */
    public function destroy(GeofencePlant $geofence_plant)
    {
        $geofence_plant->delete();

        return redirect()
            ->route('geofence-plant.index')
            ->with('success', 'Data geofence plant berhasil dihapus.');
    }

    /**
     * Konversi array exclude_department / exclude_nik dari form
     * menjadi string dipisah koma untuk disimpan ke database.
     * Contoh input: ['IT', 'HRD', 'Finance'] -> "IT,HRD,Finance"
     */
    private function arrayToCommaString(array $values): ?string
    {
        $clean = array_values(array_filter(array_map('trim', $values)));

        return empty($clean) ? null : implode(',', $clean);
    }
}