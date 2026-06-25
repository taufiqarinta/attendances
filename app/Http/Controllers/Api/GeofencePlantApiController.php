<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GeofencePlant;
use App\Http\Requests\GeofencePlantRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GeofencePlantApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = GeofencePlant::query()->where('status', GeofencePlant::STATUS_ACTIVE);

        if ($plant = $request->query('plant')) {
            $query->where('plant', $plant);
        }

        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }

        $geofencePlants = $query->get();

        // Jika dept / nik dikirim, exclude baris yang departemen/NIK-nya
        // ada di kolom exclude_department / exclude_nik (artinya pegawai
        // tersebut TIDAK perlu validasi radius / dikecualikan dari plant ini).
        $dept = $request->query('dept');
        $nik = $request->query('nik');

        if ($dept || $nik) {
            $geofencePlants = $geofencePlants->reject(function (GeofencePlant $row) use ($dept, $nik) {
                $excludedDepts = $row->exclude_department_array;
                $excludedNiks = $row->exclude_nik_array;

                $deptExcluded = $dept && in_array($dept, $excludedDepts, true);
                $nikExcluded = $nik && in_array($nik, $excludedNiks, true);

                return $deptExcluded || $nikExcluded;
            });
        }

        return response()->json([
            'success' => true,
            'data' => $geofencePlants->map(function (GeofencePlant $row) {
                return [
                    'id' => $row->id,
                    'plant' => $row->plant,
                    'plant_name' => $row->plant_name,
                    'type' => $row->type,
                    'location_checks' => (bool) $row->status,
                    'radius' => $row->radius,
                    'longitude' => $row->longitude,
                    'latitude' => $row->latitude,
                ];
            })->values(),
        ]);
    }

    /**
     * GET /api/geofence-plant/{id}
     */
    public function show(GeofencePlant $geofence_plant): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $geofence_plant,
        ]);
    }

    /**
     * POST /api/geofence-plant
     */
    public function store(GeofencePlantRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['exclude_department'] = $this->arrayToCommaString($data['exclude_department'] ?? []);
        $data['exclude_nik'] = $this->arrayToCommaString($data['exclude_nik'] ?? []);

        $geofencePlant = GeofencePlant::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Data geofence plant berhasil ditambahkan.',
            'data' => $geofencePlant,
        ], 201);
    }

    /**
     * PUT/PATCH /api/geofence-plant/{id}
     */
    public function update(GeofencePlantRequest $request, GeofencePlant $geofence_plant): JsonResponse
    {
        $data = $request->validated();
        $data['exclude_department'] = $this->arrayToCommaString($data['exclude_department'] ?? []);
        $data['exclude_nik'] = $this->arrayToCommaString($data['exclude_nik'] ?? []);

        $geofence_plant->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data geofence plant berhasil diperbarui.',
            'data' => $geofence_plant,
        ]);
    }

    /**
     * DELETE /api/geofence-plant/{id}
     */
    public function destroy(GeofencePlant $geofence_plant): JsonResponse
    {
        $geofence_plant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data geofence plant berhasil dihapus.',
        ]);
    }

    private function arrayToCommaString(array $values): ?string
    {
        $clean = array_values(array_filter(array_map('trim', $values)));

        return empty($clean) ? null : implode(',', $clean);
    }
}