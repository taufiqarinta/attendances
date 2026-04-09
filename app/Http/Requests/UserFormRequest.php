<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'id_customer' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'id_customer')->ignore($userId),
            ],
            'name' => 'required|string|max:255',
            'password' => $userId ? 'nullable|string|min:6' : 'required|string|min:6',
            'merks' => 'nullable|array',
            'merks.*' => 'exists:merks,id',
        ];
    }

    public function messages(): array
    {
        return [
            'id_customer.required' => 'ID Customer wajib diisi',
            'id_customer.unique' => 'ID Customer sudah digunakan',
            'name.required' => 'Nama wajib diisi',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
        ];
    }
}