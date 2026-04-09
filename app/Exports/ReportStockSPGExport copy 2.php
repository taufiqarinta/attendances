<?php

namespace App\Exports;

use App\Models\ReportStockSPG;
use App\Models\ReportStockSPGDetail;
use App\Models\ItemMaster;
use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class ReportStockSPGExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        // Debug: cek filter yang diterima
        \Log::info('Export filters:', $this->filters);
        
        // Tentukan bulan dan tahun untuk stock opname (bulan sebelumnya)
        $targetBulan = $this->filters['bulan'];
        $targetTahun = $this->filters['tahun'];
        
        $bulanOpname = $targetBulan - 1;
        $tahunOpname = $targetTahun;
        
        if ($bulanOpname == 0) {
            $bulanOpname = 12;
            $tahunOpname = $targetTahun - 1;
        }
        
        // 1. Ambil semua data stock opname bulan sebelumnya
        $opnameQuery = StockOpname::with(['details'])
            ->where('tahun', $tahunOpname)
            ->where('bulan', $bulanOpname)
            ->orderBy('id', 'desc')
            ->take(1);
        
        // Filter berdasarkan user jika bukan admin
        if (isset($this->filters['user_id'])) {
            $opnameQuery->where('user_id', $this->filters['user_id']);
        }
        
        $opnames = $opnameQuery->get();
        
        \Log::info('Jumlah opname ditemukan: ' . $opnames->count());
        
        // 2. Ambil semua data report stock SPG untuk bulan target
        $reportsQuery = ReportStockSPG::with(['user', 'details.itemMaster'])
            ->where('tahun', $targetTahun)
            ->where('bulan', $targetBulan)
            ->when(isset($this->filters['minggu_ke']), function($q) {
                $q->where('minggu_ke', $this->filters['minggu_ke']);
            })
            ->when(isset($this->filters['user_id']), function($q) {
                $q->where('user_id', $this->filters['user_id']);
            })
            ->orderBy('user_id', 'asc')
            ->orderBy('toko_id', 'asc')
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan', 'asc')
            ->orderBy('minggu_ke', 'asc');

        $reports = $reportsQuery->get();
        
        \Log::info('Jumlah report ditemukan: ' . $reports->count());
        
        // 3. Gabungkan semua data dari opname dan report
        $combinedData = [];
        
        // Pertama, proses semua data dari stock opname
        foreach ($opnames as $opname) {
            foreach ($opname->details as $detail) {
                $key = $opname->nama_spg . '_' . $opname->nama_toko . '_' . $detail->item_code;
                
                if (!isset($combinedData[$key])) {
                    $combinedData[$key] = [
                        'nama_spg' => $opname->nama_spg,
                        'nama_toko' => $opname->nama_toko,
                        'item_code' => $detail->item_code,
                        'item_name' => $detail->nama_barang,
                        'ukuran' => $detail->ukuran,
                        'stock_awal_bulan' => $detail->stock,
                        'catatan_stock_awal' => $detail->keterangan ?? '',
                        'qty_masuk_minggu1' => 0,
                        'qty_masuk_minggu2' => 0,
                        'qty_masuk_minggu3' => 0,
                        'qty_masuk_minggu4' => 0,
                        'qty_masuk_minggu5' => 0,
                        'catatan_minggu1' => '',
                        'catatan_minggu2' => '',
                        'catatan_minggu3' => '',
                        'catatan_minggu4' => '',
                        'catatan_minggu5' => '',
                        'stock_minggu1' => 0,
                        'stock_minggu2' => 0,
                        'stock_minggu3' => 0,
                        'stock_minggu4' => 0,
                        'stock_minggu5' => 0,
                    ];
                }
            }
        }
        
        // 4. Proses semua data dari report stock SPG
        foreach ($reports as $report) {
            foreach ($report->details as $detail) {
                $itemCode = $detail->item_code;
                $namaSpg = $report->user ? $report->user->name : '-';
                $namaToko = $report->nama_toko;
                $mingguKe = $report->minggu_ke;
                
                $key = $namaSpg . '_' . $namaToko . '_' . $itemCode;
                
                // Jika belum ada di combinedData (barang baru yang tidak ada di opname)
                if (!isset($combinedData[$key])) {
                    $combinedData[$key] = [
                        'nama_spg' => $namaSpg,
                        'nama_toko' => $namaToko,
                        'item_code' => $itemCode,
                        'item_name' => $detail->itemMaster ? $detail->itemMaster->item_name : $detail->nama_barang,
                        'ukuran' => $detail->itemMaster ? $detail->itemMaster->ukuran : $detail->ukuran,
                        'stock_awal_bulan' => 0,
                        'catatan_stock_awal' => '',
                        'qty_masuk_minggu1' => 0,
                        'qty_masuk_minggu2' => 0,
                        'qty_masuk_minggu3' => 0,
                        'qty_masuk_minggu4' => 0,
                        'qty_masuk_minggu5' => 0,
                        'catatan_minggu1' => '',
                        'catatan_minggu2' => '',
                        'catatan_minggu3' => '',
                        'catatan_minggu4' => '',
                        'catatan_minggu5' => '',
                        'stock_minggu1' => 0,
                        'stock_minggu2' => 0,
                        'stock_minggu3' => 0,
                        'stock_minggu4' => 0,
                        'stock_minggu5' => 0,
                    ];
                }
                
                // Update data berdasarkan minggu
                $combinedData[$key]['qty_masuk_minggu' . $mingguKe] = $detail->qty_masuk;
                $combinedData[$key]['catatan_minggu' . $mingguKe] = $detail->catatan;
                $combinedData[$key]['stock_minggu' . $mingguKe] = $detail->stock;
                
                // Update nama barang dan ukuran dari report jika lebih lengkap
                if (empty($combinedData[$key]['item_name']) || $combinedData[$key]['item_name'] == '') {
                    $combinedData[$key]['item_name'] = $detail->itemMaster ? $detail->itemMaster->item_name : $detail->nama_barang;
                }
                
                if (empty($combinedData[$key]['ukuran']) || $combinedData[$key]['ukuran'] == '') {
                    $combinedData[$key]['ukuran'] = $detail->itemMaster ? $detail->itemMaster->ukuran : $detail->ukuran;
                }
            }
        }
        
        // 5. Konversi ke array numerik dan urutkan
        $sortedData = array_values($combinedData);
        
        // Urutkan data: pertama berdasarkan nama_spg, lalu nama_toko, lalu item_code
        usort($sortedData, function($a, $b) {
            if ($a['nama_spg'] == $b['nama_spg']) {
                if ($a['nama_toko'] == $b['nama_toko']) {
                    return strcmp($a['item_code'], $b['item_code']);
                }
                return strcmp($a['nama_toko'], $b['nama_toko']);
            }
            return strcmp($a['nama_spg'], $b['nama_spg']);
        });
        
        \Log::info('Jumlah data export (setelah digabung): ' . count($sortedData));
        
        return new Collection($sortedData);
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama SPG',
            'Nama Toko',
            'Kode Item',
            'Nama Barang',
            'Ukuran',
            'Stock Awal Bulan',
            'Catatan Stock Awal',  // Kolom baru
            'Qty Barang Masuk Minggu 1',
            'Qty Barang Masuk Minggu 2',
            'Qty Barang Masuk Minggu 3',
            'Qty Barang Masuk Minggu 4',
            'Qty Barang Masuk Minggu 5',
            'Catatan Minggu 1',
            'Catatan Minggu 2',
            'Catatan Minggu 3',
            'Catatan Minggu 4',
            'Catatan Minggu 5',
            'Stock Minggu 1',
            'Stock Minggu 2',
            'Stock Minggu 3',
            'Stock Minggu 4',
            'Stock Minggu 5'
        ];
    }

    public function map($row): array
    {
        static $index = 0;
        $index++;

        // Helper untuk ubah null / '' / 0 jadi ''
        $fix = function($v) {
            return ($v == null || $v == '' || $v == 0) ? '' : $v;
        };

        return [
            $index,
            $fix($row['nama_spg']),
            $fix($row['nama_toko']),
            $fix($row['item_code']),
            $fix($row['item_name']),
            $fix($row['ukuran']),
            $fix($row['stock_awal_bulan']),
            $fix($row['catatan_stock_awal']),
            $fix($row['qty_masuk_minggu1']),
            $fix($row['qty_masuk_minggu2']),
            $fix($row['qty_masuk_minggu3']),
            $fix($row['qty_masuk_minggu4']),
            $fix($row['qty_masuk_minggu5']),
            $fix($row['catatan_minggu1']),
            $fix($row['catatan_minggu2']),
            $fix($row['catatan_minggu3']),
            $fix($row['catatan_minggu4']),
            $fix($row['catatan_minggu5']),
            $fix($row['stock_minggu1']),
            $fix($row['stock_minggu2']),
            $fix($row['stock_minggu3']),
            $fix($row['stock_minggu4']),
            $fix($row['stock_minggu5']),
        ];
    }


    public function styles(Worksheet $sheet)
    {
        // Update range menjadi 23 kolom: A-W
        $sheet->getStyle('A1:W1')->applyFromArray([
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
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // Auto filter
        $lastRow = $sheet->getHighestRow();
        if ($lastRow > 1) {
            $sheet->setAutoFilter('A1:W' . $lastRow);
        }

        // Wrap text untuk kolom catatan (H dan N-R)
        $sheet->getStyle('H')->getAlignment()->setWrapText(true); // Catatan Stock Awal
        $sheet->getStyle('N:R')->getAlignment()->setWrapText(true); // Catatan Minggu 1-5

        // Format untuk kolom angka
        // Stock Awal Bulan (G) dan Qty Masuk (I-M)
        $sheet->getStyle('G')->getNumberFormat()->setFormatCode('#,##0'); // Stock Awal Bulan
        $sheet->getStyle('I:M')->getNumberFormat()->setFormatCode('#,##0'); // Qty Masuk Minggu 1-5
        
        // Stock Minggu 1-5 (S-W)
        $sheet->getStyle('S:W')->getNumberFormat()->setFormatCode('#,##0');

        // Alternating row colors untuk readability
        for ($row = 2; $row <= $lastRow; $row++) {
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':W' . $row)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('F8FAFC');
            }
        }

        // Set tinggi row untuk header
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Center alignment untuk kolom angka
        $sheet->getStyle('G')->getAlignment()->setHorizontal('center'); // Stock Awal Bulan
        $sheet->getStyle('I:W')->getAlignment()->setHorizontal('center'); // Qty Masuk dan Stock
        
        // Left alignment untuk kolom teks
        $sheet->getStyle('A:F')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('H')->getAlignment()->setHorizontal('left'); // Catatan Stock Awal
        $sheet->getStyle('N:R')->getAlignment()->setHorizontal('left'); // Catatan Minggu

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // No
            'B' => 20,  // Nama SPG
            'C' => 20,  // Nama Toko
            'D' => 15,  // Kode Item
            'E' => 30,  // Nama Barang
            'F' => 12,  // Ukuran
            'G' => 15,  // Stock Awal Bulan
            'H' => 25,  // Catatan Stock Awal (baru)
            'I' => 18,  // Qty Masuk Minggu 1
            'J' => 18,  // Qty Masuk Minggu 2
            'K' => 18,  // Qty Masuk Minggu 3
            'L' => 18,  // Qty Masuk Minggu 4
            'M' => 18,  // Qty Masuk Minggu 5
            'N' => 25,  // Catatan Minggu 1
            'O' => 25,  // Catatan Minggu 2
            'P' => 25,  // Catatan Minggu 3
            'Q' => 25,  // Catatan Minggu 4
            'R' => 25,  // Catatan Minggu 5
            'S' => 15,  // Stock Minggu 1
            'T' => 15,  // Stock Minggu 2
            'U' => 15,  // Stock Minggu 3
            'V' => 15,  // Stock Minggu 4
            'W' => 15,  // Stock Minggu 5
        ];
    }
}