<?php

namespace App\Imports;

use App\Models\Guru;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuruImport implements ToModel, WithHeadingRow
{
    /**
     * Mapping kemungkinan header Excel
     */
    protected $aliases = [
        'nama_guru' => ['nama guru', 'nama', 'guru', 'nama lengkap', 'full name'],
        'nip'       => ['nip', 'no induk pegawai', 'nomor induk', 'id pegawai', 'nomor nip'],
    ];

    /**
     * Cari value dari row berdasarkan alias header
     */
    protected function getValue(array $row, string $field)
    {
        foreach ($this->aliases[$field] as $alias) {
            $key = strtolower(str_replace(' ', '_', $alias)); // normalisasi key
            if (isset($row[$key])) {
                return $row[$key];
            }
        }
        return null;
    }

    public function model(array $row)
    {
        return new Guru([
            'nama_guru' => $this->getValue($row, 'nama_guru'),
            'nip'       => $this->getValue($row, 'nip'),
        ]);
    }
}
