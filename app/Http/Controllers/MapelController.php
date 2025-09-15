<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MapelImport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MapelController extends Controller
{
    public function index(Request $request)
    {
        $query = Mapel::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nama_mapel', 'like', "%$search%")
                  ->orWhere('kode_mapel', 'like', "%$search%");
        }

        $mapel = $query->get();

        if (auth()->guard('admin')->check()) {
            return view('admin.mapel.index', compact('mapel'));
        } elseif (auth()->guard('petugas')->check()) {
            return view('petugas.mapel.index', compact('mapel'));
        }
    }

    public function create()
    {
        if (auth()->guard('admin')->check()) {
            return view('admin.mapel.create');
        } elseif (auth()->guard('petugas')->check()) {
            return view('petugas.mapel.create');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'kode_mapel' => 'required|string|max:50|unique:tbl_mapel,kode_mapel',
        ]);

        Mapel::create($request->all());

        if (auth()->guard('admin')->check()) {
            return redirect()->route('admin.mapel.index')
                ->with('success', 'Data mapel berhasil ditambahkan.');
        } elseif (auth()->guard('petugas')->check()) {
            return redirect()->route('petugas.mapel.index')
                ->with('success', 'Data mapel berhasil ditambahkan.');
        }
    }

    public function edit($id)
    {
        $mapel = Mapel::findOrFail($id);

        if (auth()->guard('admin')->check()) {
            return view('admin.mapel.edit', compact('mapel'));
        } elseif (auth()->guard('petugas')->check()) {
            return view('petugas.mapel.edit', compact('mapel'));
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'kode_mapel' => 'required|string|max:50|unique:tbl_mapel,kode_mapel,' . $id . ',id_mapel',
        ]);

        $mapel = Mapel::findOrFail($id);
        $mapel->update($request->all());

        if (auth()->guard('admin')->check()) {
            return redirect()->route('admin.mapel.index')
                ->with('success', 'Data mapel berhasil diperbarui.');
        } elseif (auth()->guard('petugas')->check()) {
            return redirect()->route('petugas.mapel.index')
                ->with('success', 'Data mapel berhasil diperbarui.');
        }
    }

    public function destroy($id)
    {
        Mapel::destroy($id);

        if (auth()->guard('admin')->check()) {
            return redirect()->route('admin.mapel.index')
                ->with('success', 'Data mapel berhasil dihapus.');
        } elseif (auth()->guard('petugas')->check()) {
            return redirect()->route('petugas.mapel.index')
                ->with('success', 'Data mapel berhasil dihapus.');
        }
    }

    // ✅ Import pakai Laravel Excel
    public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv',
    ]);

    try {
        Excel::import(new MapelImport, $request->file('file'));
    } catch (\Exception $e) {
        return back()->withErrors(['msg' => 'Gagal import: ' . $e->getMessage()]);
    }

    return redirect()->route(auth()->guard('admin')->check() ? 'admin.mapel.index' : 'petugas.mapel.index')
        ->with('success', 'Data Mapel berhasil diimport.');
}


    // ✅ Template download
    public function template()
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header kolom
    $headers = ['Nama Mapel', 'Kode Mapel'];
    $sheet->fromArray([$headers], null, 'A1');

    // Contoh data
    $sheet->fromArray([
        ['Matematika', 'MPL01'],
        ['Bahasa Indonesia', 'MPL02'],
    ], null, 'A2');

    // Styling header
    $headerStyle = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '007bff']
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical'   => Alignment::VERTICAL_CENTER
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000']
            ]
        ]
    ];
    $sheet->getStyle('A1:B1')->applyFromArray($headerStyle);

    // Auto-size kolom
    foreach (range('A', 'B') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Output file
    $filename = 'template_mapel.xlsx';
    $writer = new Xlsx($spreadsheet);
    $tempPath = public_path($filename);
    $writer->save($tempPath);

    return Response::download($tempPath)->deleteFileAfterSend(true);
}
}
