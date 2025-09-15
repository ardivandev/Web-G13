<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Petugas;
use App\Models\Barang;
use App\Models\Mapel;
use App\Models\Kategori;
use App\Models\Ruangan;
use App\Models\Peminjaman;
use App\Models\Pengembalian;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung jumlah data
        $jumlahSiswa        = Siswa::count();
        $jumlahGuru         = Guru::count();
        $jumlahPetugas      = Petugas::count();
        $jumlahBarang       = Barang::count();
        $jumlahMapel        = Mapel::count();
        $jumlahKategori     = Kategori::count();
        $jumlahRuangan      = Ruangan::count();
        $jumlahPeminjaman   = Peminjaman::count();
        $jumlahPengembalian = Pengembalian::count();

        // Ambil stok barang (key = nama_barang, value = stok)
        $stokBarang = Barang::pluck('stok', 'nama_barang');

        // Data bulanan peminjaman
        $peminjamanBulanan = Peminjaman::select(
            DB::raw('MONTH(mulai_kbm) as bulan'),
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('bulan')
        ->pluck('total', 'bulan');

        // Data bulanan pengembalian
        $pengembalianBulanan = Pengembalian::select(
            DB::raw('MONTH(tanggal_pengembalian) as bulan'),
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('bulan')
        ->pluck('total', 'bulan');

        // Lengkapi array bulan 1–12
        $peminjamanBulananLengkap   = array_replace(array_fill(1, 12, 0), $peminjamanBulanan->toArray());
        $pengembalianBulananLengkap = array_replace(array_fill(1, 12, 0), $pengembalianBulanan->toArray());

        return view('admin.dashboard.index', compact(
            'jumlahSiswa',
            'jumlahGuru',
            'jumlahPetugas',
            'jumlahBarang',
            'jumlahMapel',
            'jumlahKategori',
            'jumlahRuangan',
            'jumlahPeminjaman',
            'jumlahPengembalian',
            'stokBarang',
            'peminjamanBulananLengkap',
            'pengembalianBulananLengkap'
        ));
    }
}
