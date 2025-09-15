<?php

namespace App\Http\Controllers;

use App\Imports\GuruImport as ImportsGuruImport;
use Illuminate\Http\Request;
use App\Models\Guru;
use App\Imports\GuruImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;


class GuruController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Guru::query();

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where('nama_guru', 'like', "%$search%")
                    ->orWhere('nip', 'like', "%$search%");
            }

            $guru = $query->get();

            // Determine which view to return based on authenticated guard
            if (auth()->guard('admin')->check()) {
                return view('admin.guru.index', compact('guru'));
            } elseif (auth()->guard('petugas')->check()) {
                return view('petugas.guru.index', compact('guru'));
            }

            // If no guard is authenticated, redirect to login
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');

        } catch (\Exception $e) {
            $errorMessage = 'Terjadi kesalahan saat memuat data guru: ' . $e->getMessage();

            if (auth()->guard('admin')->check()) {
                return redirect()->route('admin.dashboard')->with('error', $errorMessage);
            } elseif (auth()->guard('petugas')->check()) {
                return redirect()->route('petugas.dashboard')->with('error', $errorMessage);
            }

            return redirect()->route('login')->with('error', $errorMessage);
        }
    }

    public function create()
    {
        try {
            if (auth()->guard('admin')->check()) {
                return view('admin.guru.create');
            } elseif (auth()->guard('petugas')->check()) {
                return view('petugas.guru.create');
            }

            return redirect()->route('login')->with('error', 'Akses tidak diizinkan.');

        } catch (\Exception $e) {
            $errorMessage = 'Terjadi kesalahan saat memuat halaman tambah guru: ' . $e->getMessage();
            return $this->redirectWithError($errorMessage);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'nama_guru' => 'required|string|max:255',
                'nip' => 'required|string|max:50|unique:tbl_guru,nip',
            ], [
                'nama_guru.required' => 'Nama guru wajib diisi.',
                'nama_guru.max' => 'Nama guru maksimal 255 karakter.',
                'nip.required' => 'NIP wajib diisi.',
                'nip.max' => 'NIP maksimal 50 karakter.',
                'nip.unique' => 'NIP sudah terdaftar, gunakan NIP lain.',
            ]);

            Guru::create($validated);

            $routeName = auth()->guard('admin')->check() ? 'admin.guru.index' : 'petugas.guru.index';
            return redirect()->route($routeName)->with('success', 'Data guru berhasil ditambahkan.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            $errorMessage = 'Gagal menambah data guru: ' . $e->getMessage();
            return back()->with('error', $errorMessage)->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $guru = Guru::findOrFail($id);

            if (auth()->guard('admin')->check()) {
                return view('admin.guru.edit', compact('guru'));
            } elseif (auth()->guard('petugas')->check()) {
                return view('petugas.guru.edit', compact('guru'));
            }

            return redirect()->route('login')->with('error', 'Akses tidak diizinkan.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $errorMessage = 'Data guru tidak ditemukan.';
            return $this->redirectWithError($errorMessage);
        } catch (\Exception $e) {
            $errorMessage = 'Terjadi kesalahan saat memuat data guru: ' . $e->getMessage();
            return $this->redirectWithError($errorMessage);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $guru = Guru::findOrFail($id);

            // Validasi input, tapi unique NIP dikecualikan untuk data yang sedang diedit
            $validated = $request->validate([
                'nama_guru' => 'required|string|max:255',
                'nip' => 'required|string|max:50|unique:tbl_guru,nip,' . $id . ',id_guru',
            ], [
                'nama_guru.required' => 'Nama guru wajib diisi.',
                'nama_guru.max' => 'Nama guru maksimal 255 karakter.',
                'nip.required' => 'NIP wajib diisi.',
                'nip.max' => 'NIP maksimal 50 karakter.',
                'nip.unique' => 'NIP sudah terdaftar, gunakan NIP lain.',
            ]);

            $guru->update($validated);

            $routeName = auth()->guard('admin')->check() ? 'admin.guru.index' : 'petugas.guru.index';
            return redirect()->route($routeName)->with('success', 'Data guru berhasil diperbarui.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $errorMessage = 'Data guru tidak ditemukan.';
            return $this->redirectWithError($errorMessage);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            $errorMessage = 'Gagal memperbarui data guru: ' . $e->getMessage();
            return back()->with('error', $errorMessage)->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $guru = Guru::findOrFail($id);
            $namaGuru = $guru->nama_guru; // Simpan nama untuk pesan
            $guru->delete();

            $routeName = auth()->guard('admin')->check() ? 'admin.guru.index' : 'petugas.guru.index';
            return redirect()->route($routeName)->with('success', "Data guru '{$namaGuru}' berhasil dihapus!");

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $errorMessage = 'Data guru tidak ditemukan.';
            return $this->redirectWithError($errorMessage);
        } catch (\Exception $e) {
            $errorMessage = 'Gagal menghapus data guru: ' . $e->getMessage();
            return $this->redirectWithError($errorMessage);
        }
    }

    public function destroyAll()
    {
        try {
            \App\Models\Guru::query()->delete(); // HAPUS SEMUA DATA

            return redirect()->route('admin.guru.index')
                ->with('success', '✅ Semua data guru berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('admin.guru.index')
                ->with('error', '❌ Gagal menghapus semua guru: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls,csv|max:2048',
            ], [
                'file.required' => 'File wajib dipilih.',
                'file.mimes' => 'File harus berformat Excel (.xlsx, .xls) atau CSV.',
                'file.max' => 'Ukuran file maksimal 2MB.',
            ]);

            Excel::import(new GuruImport, $request->file('file'));

            $routeName = auth()->guard('admin')->check() ? 'admin.guru.index' : 'petugas.guru.index';
            return redirect()->route($routeName)->with('success', 'Data guru berhasil diimport!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            $errorMessage = 'Gagal mengimport data guru: ' . $e->getMessage();
            return back()->with('error', $errorMessage);
        }
    }

    public function template()
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Data Header
            $headers = ['Nama Guru', 'NIP'];
            $sheet->fromArray([$headers]);

            // Styling Header
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '28a745']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ];
            $sheet->getStyle('A1:B1')->applyFromArray($headerStyle);

            // Styling seluruh sel (border tipis)
            $sheet->getStyle('A1:B3')->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);

            // Auto size kolom
            foreach (range('A', 'B') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Nama file
            $filename = 'template_guru_' . date('Y-m-d_H-i-s') . '.xlsx';

            // Simpan & Download
            $writer = new Xlsx($spreadsheet);
            $tempPath = storage_path('app/' . $filename);
            $writer->save($tempPath);

            return Response::download($tempPath, $filename)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            $errorMessage = 'Gagal membuat template: ' . $e->getMessage();
            return $this->redirectWithError($errorMessage);
        }
    }

    /**
     * Helper method untuk redirect dengan error message
     */
    private function redirectWithError($errorMessage)
    {
        if (auth()->guard('admin')->check()) {
            return redirect()->route('admin.guru.index')->with('error', $errorMessage);
        } elseif (auth()->guard('petugas')->check()) {
            return redirect()->route('petugas.guru.index')->with('error', $errorMessage);
        }

        return redirect()->route('login')->with('error', $errorMessage);
    }
}
