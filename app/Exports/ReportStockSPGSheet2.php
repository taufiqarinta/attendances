<?php

namespace App\Exports;

use App\Models\ReportStockSPG;
use App\Models\ReportStockSPGDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportStockSPGSheet2 implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Laporan Stock SPG';
    }

    public function collection()
    {
        $query = ReportStockSPG::with(['details', 'toko']);
        
        // Filter berdasarkan role user
        if (isset($this->filters['user_role']) && $this->filters['user_role'] == 0) {
            $query->where('user_id', $this->filters['user_id']);
        }
        
        // Filter berdasarkan tahun
        if (isset($this->filters['tahun'])) {
            $query->where('tahun', $this->filters['tahun']);
        }
        
        // Filter berdasarkan bulan
        if (isset($this->filters['bulan'])) {
            $query->where('bulan', $this->filters['bulan']);
        }
        
        // Filter berdasarkan minggu
        if (isset($this->filters['minggu_ke']) && $this->filters['minggu_ke']) {
            $query->where('minggu_ke', $this->filters['minggu_ke']);
        }
        
        // Filter berdasarkan search
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('kode_report', 'like', '%' . $search . '%')
                ->orWhere('nama_spg', 'like', '%' . $search . '%')
                ->orWhereHas('toko', function($q) use ($search) {
                    $q->where('nama_toko', 'like', '%' . $search . '%');
                });
            });
        }
        
        return $query->orderBy('tahun', 'desc')
                    ->orderBy('bulan', 'desc')
                    ->orderBy('minggu_ke', 'desc')
                    ->get();
    }

    public function headings(): array
    {
        return [
            'Kode Report',
            'Tahun',
            'Bulan',
            'Minggu Ke',
            'Tanggal Report',
            'Nama SPG',
            'Nama Toko',
            'Kode Item',
            'Nama Barang',
            'Ukuran',
            'Stock',
            'Qty Masuk',
            'Catatan',
        ];
    }

    public function map($report): array
    {
        $fix = function($v) {
            return ($v == null || $v == '' || $v == 0) ? '' : $v;
        };

        $bulanNama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $rows = [];

        if ($report->details->count() > 0) {
            foreach ($report->details as $detail) {
                $rows[] = [
                    $fix($report->kode_report),
                    $fix($report->tahun),
                    $fix($bulanNama[$report->bulan] ?? $report->bulan),
                    $fix($report->minggu_ke),
                    $fix($report->tanggal ? $report->tanggal->format('d/m/Y') : ''),
                    $fix($report->nama_spg),
                    $fix($report->toko->nama_toko ?? ''),
                    $fix($detail->item_code),
                    $fix($detail->nama_barang),
                    $fix($detail->ukuran),
                    $fix($detail->stock),
                    $fix($detail->qty_masuk),
                    $fix($detail->catatan),
                ];
            }
        } else {
            $rows[] = [
                $fix($report->kode_report),
                $fix($report->tahun),
                $fix($bulanNama[$report->bulan] ?? $report->bulan),
                $fix($report->minggu_ke),
                $fix($report->tanggal ? $report->tanggal->format('d/m/Y') : ''),
                $fix($report->nama_spg),
                $fix($report->toko->nama_toko ?? ''),
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
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Auto filter
        $sheet->setAutoFilter('A1:M' . ($sheet->getHighestRow()));

        // Wrap text untuk kolom catatan
        $sheet->getStyle('M:M')->getAlignment()->setWrapText(true);

        // Format alignment untuk kolom numerik
        $sheet->getStyle('K:L')->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        );

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Kode Report
            'B' => 10, // Tahun
            'C' => 12, // Bulan
            'D' => 10, // Minggu Ke
            'E' => 15, // Tanggal Report
            'F' => 20, // Nama SPG
            'G' => 20, // Nama Toko
            'H' => 15, // Kode Item
            'I' => 30, // Nama Barang
            'J' => 10, // Ukuran
            'K' => 12, // Stock
            'L' => 12, // Qty Masuk
            'M' => 30, // Catatan
        ];
    }
}