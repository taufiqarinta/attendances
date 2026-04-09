<?php

namespace App\Exports;

use App\Models\FormReportSPG;
use App\Models\FormReportSPGDetail;
use App\Models\ReportStockSPG;
use App\Models\ReportStockSPGDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportStockSPGExport implements WithMultipleSheets
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        return [
            'Laporan Stock Peritem' => new ReportStockSPGSheet1($this->filters),
            'Laporan Stock SPG' => new ReportStockSPGSheet2($this->filters),
        ];
    }
}