<?php
namespace App\Imports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements ToModel, WithHeadingRow
{
    public function headingRow(): int
    {
        return 1; // header ada di baris pertama
    }

    public function model(array $row)
    {
        // Normalisasi header ke lowercase
        $row = array_change_key_case($row, CASE_LOWER);

        // Daftar kemungkinan nama header
        $mapNama = ['nama_siswa', 'nama siswa', 'nama', 'namasiswa'];
        $mapNis  = ['nis', 'nisn', 'nis/nisn'];
        $mapKls  = ['kelas', 'kls', 'kls/jur', 'jur', 'kls/lp'];

        // Fungsi bantu cari kolom
        $findValue = function($row, $possibleKeys) {
            foreach ($possibleKeys as $key) {
                if (isset($row[strtolower($key)]) && trim($row[strtolower($key)]) !== '') {
                    return trim($row[strtolower($key)]);
                }
            }
            return null;
        };

        // Ambil nilai
        $nama  = $findValue($row, $mapNama);
        $nis   = $findValue($row, $mapNis);
        $kelas = $findValue($row, $mapKls);

        // Kalau semua kosong → skip
        if (empty($nama) || empty($nis) || empty($kelas)) {
            return null;
        }

        // Cegah duplikat berdasarkan NIS
        if (Siswa::where('nis', $nis)->exists()) {
            return null;
        }

        return new Siswa([
            'nama_siswa' => $nama,
            'nis'        => $nis,
            'kelas'      => $kelas,
        ]);
    }
}
