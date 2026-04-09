<?php

namespace App\Exports;

use App\Models\FormReportSPG;
use App\Models\FormReportSPGDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Protection;

class ReportSPGExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
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

        return [];
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

    /**
     * Register events untuk proteksi worksheet
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Dapatkan worksheet
                $worksheet = $event->sheet->getDelegate();
                
                // Buat objek protection
                $protection = $worksheet->getProtection();
                
                // Set password untuk proteksi (opsional)
                // $protection->setPassword('password123');
                
                // Aktifkan proteksi sheet
                $protection->setSheet(true);
                
                // Izinkan beberapa aksi tertentu (opsional)
                $protection->setSelectLockedCells(true);  // Izinkan memilih sel yang terkunci
                $protection->setSelectUnlockedCells(true); // Izinkan memilih sel yang tidak terkunci
                $protection->setFormatCells(false);        // Tidak izinkan format sel
                $protection->setFormatColumns(false);      // Tidak izinkan format kolom
                $protection->setFormatRows(false);         // Tidak izinkan format baris
                $protection->setInsertRows(false);         // Tidak izinkan insert baris
                $protection->setInsertColumns(false);      // Tidak izinkan insert kolom
                $protection->setInsertHyperlinks(false);   // Tidak izinkan insert hyperlink
                $protection->setDeleteRows(false);         // Tidak izinkan hapus baris
                $protection->setDeleteColumns(false);      // Tidak izinkan hapus kolom
                $protection->setSort(false);               // Tidak izinkan sorting
                $protection->setAutoFilter(false);         // Tidak izinkan auto filter
                $protection->setPivotTables(false);        // Tidak izinkan pivot tables
                $protection->setObjects(false);            // Tidak izinkan edit objects
                $protection->setScenarios(false);          // Tidak izinkan edit scenarios
                
                // Set nama worksheet agar lebih jelas
                $worksheet->setTitle('Data Report SPG');
                
                // Tambahkan catatan bahwa sheet terkunci (opsional)
                $worksheet->setCellValue('J1', 'Sheet terkunci - Copy ke sheet baru untuk edit');
                $event->sheet->getStyle('J1')->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'color' => ['rgb' => 'FF0000']
                    ]
                ]);
            },
        ];
    }
}