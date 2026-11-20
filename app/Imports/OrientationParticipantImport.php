<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OrientationParticipantImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    protected array $rows = [];

    public function collection(Collection $rows): void
    {
        $this->rows = $rows->toArray();
    }

    public function totalRows(): int
    {
        return count($this->rows);
    }

    /**
     * Baris peserta yang sudah dinormalisasi (semua field opsional).
     */
    public function participants(): array
    {
        $result = [];
        $seenNiks = [];

        foreach ($this->rows as $row) {
            $row = (array) $row;

            $participant = [
                'nik' => $this->stringValue($row['nik'] ?? $row['nik_karyawan'] ?? null),
                'nama' => $this->stringValue($row['nama'] ?? $row['nama_karyawan'] ?? null),
                'jabatan' => $this->stringValue($row['jabatan'] ?? $row['posisi'] ?? null),
                'dept' => $this->stringValue($row['dept'] ?? $row['departemen'] ?? $row['department'] ?? null),
            ];

            // Lewati baris kosong.
            if (!array_filter($participant, fn($value) => $value !== null)) {
                continue;
            }

            // NIK tidak boleh duplikat.
            if ($participant['nik'] !== null) {
                if (isset($seenNiks[$participant['nik']])) {
                    continue;
                }
                $seenNiks[$participant['nik']] = true;
            }

            $result[] = $participant;
        }

        return $result;
    }

    private function stringValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_float($value) && floor($value) === $value) {
            $value = sprintf('%.0f', $value);
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
