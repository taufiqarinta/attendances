<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrientationParticipantTemplateExport implements FromArray, WithHeadings, WithColumnWidths, WithStyles
{
    public function array(): array
    {
        return [
            ['12345678', 'Budi Santoso', 'Operator Produksi', 'Produksi'],
            ['87654321', 'Siti Aminah', 'Staff HRD', 'HRD'],
            ['', 'Contoh Peserta Tanpa NIK', '', ''],
        ];
    }

    public function headings(): array
    {
        return ['NIK', 'Nama', 'Jabatan', 'Dept'];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 32,
            'C' => 28,
            'D' => 24,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Format kolom NIK sebagai teks agar angka 0 di depan tidak hilang.
        $sheet->getStyle('A2:A1000')->getNumberFormat()->setFormatCode('@');

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'B91C1C'],
                ],
            ],
        ];
    }
}
