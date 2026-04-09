<?php

namespace App\Imports;

use App\Models\DaftarHarga;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class DaftarHargaImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {
        // Mapping kolom Excel ke kolom database
        return new DaftarHarga([
            'type' => $row['type'] ?? $row['TYPE'] ?? null,
            'kw' => $row['kw'] ?? $row['KW'] ?? null,
            'brand' => $row['brand'] ?? $row['Brand'] ?? null,
            'ukuran' => $row['ukuran'] ?? $row['Ukuran'] ?? null,
            'karton' => $row['karton'] ?? $row['Karton'] ?? null,
            'kategori' => $row['kategori'] ?? $row['Kategori'] ?? null,
            'kel_harga_miss2' => $row['kel_harga_miss2'] ?? $row['kel_harga_miss2'] ?? $row['KEL HARGA MISS2'] ?? null,
            'harga_franco' => $this->parseDecimal($row['pl_nett_franco'] ?? $row['PL Nett (Franco)'] ?? $row['harga_franco'] ?? 0),
            'harga_loco' => $this->parseDecimal($row['pl_nett_loco'] ?? $row['PL Nett (Loco)'] ?? $row['harga_loco'] ?? 0),
        ]);
    }

    private function parseDecimal($value)
    {
        if (is_numeric($value)) {
            return $value;
        }
        
        // Handle format currency atau string
        $value = str_replace(['Rp', ',', ' '], '', $value);
        return floatval($value);
    }

    public function rules(): array
    {
        return [
            '*.type' => 'required|string|max:100',
            '*.kw' => 'nullable|string|max:50',
            '*.brand' => 'nullable|string|max:100',
            '*.ukuran' => 'nullable|string|max:50',
            '*.karton' => 'nullable|string|max:128',
            '*.kategori' => 'nullable|string|max:100',
            '*.kel_harga_miss2' => 'nullable|string|max:100',
            '*.pl_nett_franco' => 'nullable|numeric',
            '*.pl_nett_loco' => 'nullable|numeric',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.type.required' => 'Kolom TYPE harus diisi',
            '*.type.string' => 'Kolom TYPE harus berupa teks',
        ];
    }
}