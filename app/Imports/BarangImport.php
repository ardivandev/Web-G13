<?php

namespace App\Imports;

use App\Models\Barang;
use App\Models\Kategori;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class BarangImport implements ToModel, WithStartRow
{
    public function startRow(): int
    {
        return 2; // Mulai dari baris ke-2 (header di-skip)
    }

    public function model(array $row)
    {
        return new Barang([
            'nama_barang' => $row[0], // Kolom A
            'spesifikasi' => $row[1], // Kolom B
            'stok'        => $row[2], // Kolom C
            'id_kategori' => Kategori::where('nama_kategori', $row[3])->value('id_kategori') ?? null, // Kolom D
        ]);
    }
}
