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
        'nama_guru' => ['nama guru', 'nama', 'guru', 'nama lengkap', 'full name', 'Nama Guru', 'nama_guru'],
        'nip'       => ['nip', 'no induk pegawai', 'nomor induk', 'id pegawai', 'nomor nip', 'NIP'],
    ];

    /**
     * Cari value dari row berdasarkan alias header
     */
    protected function getValue(array $row, string $field)
    {
        foreach ($this->aliases[$field] as $alias) {
            // Normalisasi: lowercase + hapus spasi
            $key = strtolower(str_replace(' ', '', $alias));

            foreach ($row as $rowKey => $value) {
                $normalizedKey = strtolower(str_replace(' ', '', $rowKey));
                if ($normalizedKey === $key) {
                    return $value;
                }
            }
        }
        return null;
    }

    public function model(array $row)
{
    $nama = $this->getValue($row, 'nama_guru');
    $nip  = $this->getValue($row, 'nip');

    // Debug sementara (tulis ke laravel.log)
    \Log::info('Row Guru Import', [
        'row'  => $row,
        'nama' => $nama,
        'nip'  => $nip,
    ]);

    if (empty($nama)) {
        \Log::warning("Skip row karena nama kosong", $row);
        return null;
    }

    $nip = !empty($nip) ? $nip : null;

    if ($nip && Guru::where('nip', $nip)->exists()) {
        \Log::warning("Skip row karena NIP duplikat: " . $nip);
        return null;
    }

    return new Guru([
        'nama_guru' => $nama,
        'nip'       => $nip,
    ]);
}

}
