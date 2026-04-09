<?php

namespace App\Exports;

use App\Models\FormReportSPG;
use App\Models\FormReportSPGDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportSPGExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = FormReportSPG::with(['details', 'user']);
        
        // Filter berdasarkan role user
        if ($this->filters['user_role'] == 0) {
            $query->where('user_id', $this->filters['user_id']);
        }
        
        // Filter tanggal
        if (!empty($this->filters['start_date'])) {
            $query->whereDate('tanggal', '>=', $this->filters['start_date']);
        }
        
        if (!empty($this->filters['end_date'])) {
            $query->whereDate('tanggal', '<=', $this->filters['end_date']);
        }
        
        // Filter nama SPG
        if (!empty($this->filters['nama_spg'])) {
            $query->where('nama_spg', 'like', '%' . $this->filters['nama_spg'] . '%');
        }
        
        return $query->orderBy('tanggal', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    public function headings(): array
    {
        return [
            'Kode Report',
            'Tanggal Report',
            'Nama SPG',
            'Kode Item',
            'Nama Barang',
            'Ukuran',
            'Qty Terjual (Box)',
            'Qty Masuk (Box)',
            'Catatan',
        ];
    }

    public function map($report): array
    {
        $rows = [];
        
        if ($report->details->count() > 0) {
            foreach ($report->details as $index => $detail) {
                $rows[] = [
                    $report->kode_report,
                    $report->tanggal->format('d/m/Y'),
                    $report->nama_spg,
                    $detail->item_code,
                    $detail->nama_barang,
                    $detail->ukuran ?? '-',
                    $detail->qty_terjual,
                    $detail->qty_masuk,
                    $detail->catatan ?? '-',
                ];
            }
        } else {
            $rows[] = [
                $report->kode_report,
                $report->tanggal->format('d/m/Y'),
                $report->nama_spg,
                '',
                'Tidak ada data detail',
                '',
                '',
                '',
                '',
            ];
        }
        
        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Auto filter
        $sheet->setAutoFilter('A1:I' . ($sheet->getHighestRow()));

        // Wrap text untuk kolom catatan
        $sheet->getStyle('I:I')->getAlignment()->setWrapText(true);

        return [
            // Style untuk total rows
            // 'J' => ['font' => ['bold' => true]],
            // 'K' => ['font' => ['bold' => true]],
            // 'L' => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Kode Report
            'B' => 15, // Tanggal Report
            'C' => 20, // Nama SPG
            'D' => 15, // Kode Item
            'E' => 30, // Nama Barang
            'F' => 10, // Ukuran
            'G' => 15, // Qty Terjual
            'H' => 15, // Qty Masuk
            'I' => 30, // Catatan
        ];
    }
}