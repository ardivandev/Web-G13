<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Imports\BarangImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::with('kategori');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nama_barang', 'like', "%$search%")
                  ->orWhere('spesifikasi', 'like', "%$search%");
        }

        $barang = $query->get();

        if (auth()->guard('admin')->check()) {
            return view('admin.barang.index', compact('barang'));
        } elseif (auth()->guard('petugas')->check()) {
            return view('petugas.barang.index', compact('barang'));
        }
    }

    public function create()
    {
        $kategori = Kategori::all();

        if (auth()->guard('admin')->check()) {
            return view('admin.barang.create', compact('kategori'));
        } elseif (auth()->guard('petugas')->check()) {
            return view('petugas.barang.create', compact('kategori'));
        }
    }

    public function store(Request $request)
{
    $request->validate([
        'nama_barang' => 'required|string|max:255',
        'spesifikasi' => 'required|string',
        'stok'        => 'required|integer',
        'id_kategori' => 'required|exists:tbl_kategori_barang,id_kategori',
        'gambar'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // validasi gambar
    ]);

    try {
        $data = $request->all();

        // Kalau ada upload gambar
        if ($request->hasFile('gambar')) {
            $namaFile = time() . '.' . $request->gambar->extension();
            $request->gambar->move(public_path('images/barang'), $namaFile);
            $data['gambar'] = $namaFile;
        }

        Barang::create($data);

        return redirect()->route(auth()->guard('admin')->check() ? 'admin.barang.index' : 'petugas.barang.index')
            ->with('success', 'Data Barang berhasil ditambahkan!');
    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->withErrors(['msg' => 'Data gagal disimpan: ' . $e->getMessage()]);
    }
}


    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        $kategori = Kategori::all();

        if (auth()->guard('admin')->check()) {
            return view('admin.barang.edit', compact('barang','kategori'));
        } elseif (auth()->guard('petugas')->check()) {
            return view('petugas.barang.edit', compact('barang','kategori'));
        }
    }

    public function update(Request $request, $id)
{
    $barang = Barang::findOrFail($id);

    $request->validate([
        'nama_barang' => 'required|string|max:255',
        'spesifikasi' => 'required|string',
        'stok'        => 'required|integer',
        'id_kategori' => 'required|exists:tbl_kategori_barang,id_kategori',
        'gambar'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // validasi gambar
    ]);

    try {
        $data = $request->all();

        // Kalau ada upload gambar baru
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama kalau ada
            if ($barang->gambar && file_exists(public_path('images/barang/' . $barang->gambar))) {
                unlink(public_path('images/barang/' . $barang->gambar));
            }

            // Upload gambar baru
            $namaFile = time() . '.' . $request->gambar->extension();
            $request->gambar->move(public_path('images/barang'), $namaFile);
            $data['gambar'] = $namaFile;
        } else {
            // Kalau tidak upload gambar, pakai gambar lama
            $data['gambar'] = $barang->gambar;
        }

        $barang->update($data);

        return redirect()->route(auth()->guard('admin')->check() ? 'admin.barang.index' : 'petugas.barang.index')
            ->with('success', 'Data Barang berhasil diperbarui!');
    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->withErrors(['msg' => 'Data gagal diperbarui: ' . $e->getMessage()]);
    }
}


    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();

        if (auth()->guard('admin')->check()) {
            return redirect()->route('admin.barang.index')
                ->with('success', 'Data barang berhasil dihapus!');
        } elseif (auth()->guard('petugas')->check()) {
            return redirect()->route('petugas.barang.index')
                ->with('success', 'Data barang berhasil dihapus!');
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new BarangImport, $request->file('file'));

            if (auth()->guard('admin')->check()) {
                return redirect()->route('admin.barang.index')
                    ->with('success', 'Data barang berhasil diimport!');
            } elseif (auth()->guard('petugas')->check()) {
                return redirect()->route('petugas.barang.index')
                    ->with('success', 'Data barang berhasil diimport!');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['msg' => 'Import gagal: ' . $e->getMessage()]);
        }
    }

    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = ['Nama Barang', 'Spesifikasi', 'Stok', 'Kategori'];
        $sheet->fromArray([$headers]);

        // Styling Header
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '007bff']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        // Styling border all cell contoh sampai baris 3
        $sheet->getStyle('A1:D3')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Auto size kolom
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'template_barang.xlsx';
        $writer = new Xlsx($spreadsheet);
        $tempPath = public_path($filename);
        $writer->save($tempPath);

        return Response::download($tempPath)->deleteFileAfterSend(true);
    }
}
