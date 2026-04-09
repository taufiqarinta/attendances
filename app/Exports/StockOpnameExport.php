<?php

namespace App\Exports;

use App\Models\StockOpname;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockOpnameExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = StockOpname::with(['details', 'toko', 'user']);
        
        if (isset($this->filters['tahun'])) {
            $query->where('tahun', $this->filters['tahun']);
        }
        
        if (isset($this->filters['bulan'])) {
            $query->where('bulan', $this->filters['bulan']);
        }
        
        if (isset($this->filters['user_id']) && $this->filters['user_role'] == 0) {
            $query->where('user_id', $this->filters['user_id']);
        }
        
        if (isset($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('kode_opname', 'like', '%' . $search . '%')
                  ->orWhere('nama_spg', 'like', '%' . $search . '%')
                  ->orWhereHas('toko', function($q) use ($search) {
                      $q->where('nama_toko', 'like', '%' . $search . '%');
                  });
            });
        }
        
        return $query->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Kode Opname',
            'Tanggal',
            'Nama Toko',
            'Tahun',
            'Bulan',
            // 'Status',
            'Nama SPG',
            'Item Code',
            'Nama Barang',
            'Ukuran',
            'Stock',
            'Keterangan'
        ];
    }

    public function map($opname): array
    {
        // Helper untuk ubah null / '' / '0' / 0 jadi ''
        $fix = function($v) {
            return ($v == null || $v == '' || $v == 0) ? '' : $v;
        };

        $rows = [];
        
        foreach ($opname->details as $detail) {
            $rows[] = [
                $fix($opname->kode_opname),
                $fix($opname->tanggal->format('d/m/Y')),
                $fix($opname->nama_toko),
                $fix($opname->tahun),
                $fix($this->getMonthName($opname->bulan)),
                // $fix(ucfirst($opname->status)),
                $fix($opname->nama_spg),
                $fix($detail->item_code),
                $fix($detail->nama_barang),
                $fix($detail->ukuran),
                $fix($detail->stock),
                $fix($detail->keterangan),
            ];
        }
        
        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5']
                ]
            ],
        ];
    }

    private function getMonthName($month)
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $months[$month] ?? $month;
    }
}