<?php

namespace App\Http\Requests;

use App\Models\GeofencePlant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class GeofencePlantRequest extends FormRequest
{
    /**
     * Set true karena otorisasi (siapa yang boleh akses) ditangani
     * lewat middleware route, bukan di sini. Sesuaikan jika project
     * Anda memakai Policy/Gate khusus untuk resource ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Saat update, kolom unique (jika ada) perlu mengecualikan record ini sendiri.
        $geofenceId = $this->route('geofence_plant')?->id ?? $this->route('id');

        return [
            'plant' => ['required', 'string', 'max:50'],
            'plant_name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:' . implode(',', GeofencePlant::TYPES)],
            'radius' => ['required', 'numeric', 'min:0'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            // exclude_department & exclude_nik disimpan sebagai string
            // dipisah koma di DB. Input dari form berupa array (checkbox/
            // tag-input), lalu di-convert ke string di controller sebelum
            // disimpan — lihat prepareExcludeDepartmentString() di Controller.
            'exclude_department' => ['nullable', 'array'],
            'exclude_department.*' => ['nullable', 'string', 'max:100'],
            'exclude_nik' => ['nullable', 'array'],
            'exclude_nik.*' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:0,1'],
        ];
    }

    public function messages(): array
    {
        return [
            'plant.required' => 'Kode plant wajib diisi.',
            'plant_name.required' => 'Nama plant wajib diisi.',
            'type.required' => 'Jenis absensi (IN/OUT) wajib dipilih.',
            'type.in' => 'Jenis absensi hanya boleh IN atau OUT.',
            'radius.required' => 'Radius wajib diisi.',
            'radius.numeric' => 'Radius harus berupa angka.',
            'longitude.required' => 'Longitude wajib diisi.',
            'longitude.between' => 'Longitude harus di antara -180 dan 180.',
            'latitude.required' => 'Latitude wajib diisi.',
            'latitude.between' => 'Latitude harus di antara -90 dan 90.',
            'status.required' => 'Status wajib dipilih.',
        ];
    }

    /**
     * Jika request berupa API (mengharapkan JSON), kembalikan error
     * validasi dalam format JSON yang konsisten alih-alih redirect.
     */
    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson() || $this->is('api/*')) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422));
        }

        parent::failedValidation($validator);
    }
}