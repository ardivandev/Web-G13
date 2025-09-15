<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use App\Imports\SiswaImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Facades\DB;


class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nama_siswa', 'like', "%$search%")
                  ->orWhere('nis', 'like', "%$search%");
        }

        $siswa = $query->get();

        // render halaman berdasarkan role
        if (auth()->guard('admin')->check()) {
            return view('admin.siswa.index', compact('siswa'));
        } elseif (auth()->guard('petugas')->check()) {
            return view('petugas.siswa.index', compact('siswa'));
        }
    }

    // ===================== ADMIN ONLY =====================
    public function create()
    {
        return view('admin.siswa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_siswa' => 'required',
            'nis'        => 'required|unique:tbl_siswa',
            'kelas'      => 'required',
        ]);

        try {
            Siswa::create($request->all());
            return redirect()->route('admin.siswa.index')
                ->with('success', 'Data siswa berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['msg' => 'Data gagal disimpan: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $siswa = Siswa::findOrFail($id);
        return view('admin.siswa.edit', compact('siswa'));
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $request->validate([
            'nama_siswa' => 'required',
            'nis'        => 'required|unique:tbl_siswa,nis,' . $id . ',id_siswa',
            'kelas'      => 'required',
        ]);

        try {
            $siswa->update($request->all());
            return redirect()->route('admin.siswa.index')
                ->with('success', 'Data siswa berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['msg' => 'Data gagal diperbarui: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();
        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil dihapus!');
    }

       public function destroyAll()
    {
        try {
            DB::table('tbl_siswa')->delete();
            return redirect()->route('admin.siswa.index')->with('success', 'Semua data siswa berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('admin.siswa.index')->with('error', 'Gagal menghapus semua data: ' . $e->getMessage());
        }

    }

    public function import(Request $request)
    {
        // Validasi file
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|',
        ], [
            'file.required' => 'File wajib diunggah.',
            'file.mimes'    => 'Format file harus .xlsx, .xls, atau .csv.',
        ]);

        try {
            Excel::import(new SiswaImport, $request->file('file'));

            return redirect()->route('admin.siswa.index')
                ->with('success', 'Data siswa berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['msg' => 'Import gagal: ' . $e->getMessage()]);
        }
    }

    /**
     * Download template Excel untuk import siswa
     */
    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ===== HEADER =====
        // Gunakan nama header yang sesuai dengan kemungkinan importer
        $headers = ['NAMA SISWA', 'NIS/NISN', 'KLS/JUR'];
        $sheet->fromArray([$headers]);

        // ===== STYLING HEADER =====
        $headerStyle = [
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 12
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '28a745']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000']
                ]
            ]
        ];
        $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);

        // ===== CONTOH DATA =====
        $sampleData = [
            ['Budi Santoso', '12345', 'XII RPL 1'],
            ['Siti Aminah', '12346', 'XII RPL 2'],
        ];
        $sheet->fromArray($sampleData, null, 'A2');

        // Border tipis untuk seluruh range
        $sheet->getStyle('A1:C3')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000']
                ]
            ]
        ]);

        // Auto size kolom
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Nama file
        $filename = 'template_siswa.xlsx';

        // Simpan sementara & download
        $writer = new Xlsx($spreadsheet);
        $tempPath = public_path($filename);
        $writer->save($tempPath);

        return Response::download($tempPath)->deleteFileAfterSend(true);
    }
}
