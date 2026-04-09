<?php

namespace App\Http\Requests;

use App\Models\SubProject;
use Illuminate\Foundation\Http\FormRequest;

class StoreDailyActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_pt' => 'required|string',
            // 'nama_sub_project' => 'required|string|in:-,' . implode(',', SubProject::pluck('nama_sub_project')->toArray()),
            'tanggal' => 'required|date',
            'waktu' => 'required|numeric|min:0|max:8',
            'keterangan' => 'required|string',
            'cuti' => 'nullable|in:0,1',
            'ijin' => 'nullable|in:0,1',
            'sakit' => 'nullable|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'waktu.max' => 'waktu yang anda inputkan lebih, beristirahatlah',
        ];
    }
}
