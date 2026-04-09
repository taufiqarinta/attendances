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
        $query = FormReportSPG::with(['details', 'user', 'toko']);
        
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
            'Nama Toko',
            'Total Customer Pada Hari ini',
            'Customer Bertransaksi',
            'Customer Lost Sale',
            'Analisa Penyebab Lost Sale',
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
        // Helper untuk ubah null / '' / '0' / 0 jadi ''
        $fix = function($v) {
            return ($v == null || $v == '' || $v == 0) ? '' : $v;
        };

        $rows = [];

        if ($report->details->count() > 0) {
            foreach ($report->details as $index => $detail) {
                $rows[] = [
                    $fix($report->kode_report),
                    $fix($report->tanggal->format('d/m/Y')),
                    $fix($report->nama_spg),
                    $fix($report->toko->nama_toko),
                    $fix($report->total_customer),
                    $fix($report->customer_transaksi),
                    $fix($report->customer_lost_sale),
                    $fix($report->analisa_lost_sale),
                    $fix($detail->item_code),
                    $fix($detail->nama_barang),
                    $fix($detail->ukuran),
                    $fix($detail->qty_terjual),
                    $fix($detail->qty_masuk),
                    $fix($detail->catatan),
                ];
            }
        } else {
            $rows[] = [
                $fix($report->kode_report),
                $fix($report->tanggal->format('d/m/Y')),
                $fix($report->nama_spg),
                $fix($report->toko->nama_toko),
                $fix($report->total_customer),
                $fix($report->customer_transaksi),
                $fix($report->customer_lost_sale),
                $fix($report->analisa_lost_sale),
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
        $sheet->getStyle('A1:N1')->applyFromArray([
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
        $sheet->setAutoFilter('A1:N' . ($sheet->getHighestRow()));

        // Wrap text untuk kolom catatan
        $sheet->getStyle('M:M')->getAlignment()->setWrapText(true);

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
            'D' => 20, // Nama Toko
            'E' => 10, // Kode Item
            'F' => 10, // Nama Barang
            'G' => 10, // Ukuran
            'H' => 30, // Qty Terjual
            'I' => 20, // Qty Masuk
            'J' => 20, // Catatan
            'K' => 15, // Catatan
            'L' => 10, // Catatan
            'M' => 10, // Catatan
            'N' => 30, // Catatan
        ];
    }
}