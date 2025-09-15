<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Support\Facades\Response;

class LaporanController extends Controller
{
    // ... existing methods (index, barangSering) remain the same ...
   public function exportPdf(Request $request)
{
    $startDate = $request->get('start_date');
    $endDate   = $request->get('end_date');

    // Ambil data dengan filter yang konsisten
    $data = $this->getData($request);

    // Ambil data peminjaman lengkap dengan relasi menggunakan filter yang sama
    $detailPeminjaman = Peminjaman::with(['detail.barang', 'siswa', 'guru', 'mapel', 'ruangan', 'pengembalian'])
        ->whereHas('pengembalian', function($query) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $query->whereBetween('tanggal_pengembalian', [$startDate, $endDate]);
            }
        })
        ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
            $q->whereHas('pengembalian', function($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_pengembalian', [$startDate, $endDate]);
            });
        })
        ->orderBy('tanggal_pinjam', 'desc')
        ->get();

    $pdf = Pdf::loadView('pdf.laporan_gudang', [
        'startDate' => $startDate,
        'endDate' => $endDate,
        'totalPeminjaman' => $data['totalPeminjaman'],
        'totalPengembalian' => $data['totalPengembalian'],
        'totalBarang' => $data['totalBarang'],
        'totalStok' => $data['totalStok'],
        'barangSering' => $data['barangSering'],
        'barangMenipis' => $data['barangMenipis'],
        'siswaTerbanyak' => $data['siswaTerbanyak'],
        'guruTerbanyak' => $data['guruTerbanyak'],
        'ruanganTerbanyak' => $data['ruanganTerbanyak'],
        'peminjamanTerlambat' => $data['peminjamanTerlambat'],
        'detailPeminjaman' => $detailPeminjaman,
    ]);

    return $pdf->download('laporan_gudang.pdf');
    }

    public function exportExcel(Request $request)
    {
        $data = $this->getData($request);

        $spreadsheet = new Spreadsheet();

        // Remove default worksheet and create custom ones
        $spreadsheet->removeSheetByIndex(0);

        // ==================== SHEET 1: RINGKASAN ====================
        $summarySheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Ringkasan');
        $spreadsheet->addSheet($summarySheet, 0);

        $this->createSummarySheet($summarySheet, $data);

        // ==================== SHEET 2: BARANG POPULER ====================
        $itemsSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Barang Populer');
        $spreadsheet->addSheet($itemsSheet, 1);

        $this->createItemsSheet($itemsSheet, $data);

        // ==================== SHEET 3: ANALISIS PENGGUNA ====================
        $usersSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Analisis Pengguna');
        $spreadsheet->addSheet($usersSheet, 2);

        $this->createUsersSheet($usersSheet, $data);

        // ==================== SHEET 4: PERINGATAN ====================
        $alertsSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Peringatan');
        $spreadsheet->addSheet($alertsSheet, 3);

        $this->createAlertsSheet($alertsSheet, $data);

        // Set active sheet to first sheet
        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'Laporan_Gudang_Peminjaman_'.date('Y-m-d_H-i-s').'.xlsx';
        $tempPath = storage_path('app/temp/' . $filename);

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return Response::download($tempPath)->deleteFileAfterSend(true);
    }

    private function createSummarySheet($sheet, $data)
    {
        // Header
        $sheet->setCellValue('A1', 'LAPORAN GUDANG PEMINJAMAN');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F8FAFC']]
        ]);

        $periode = $this->getPeriodeText($data['startDate'], $data['endDate']);
        $sheet->setCellValue('A2', 'Periode: ' . $periode);
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->setCellValue('A3', 'Generated: ' . now()->format('d F Y, H:i') . ' WIB');
        $sheet->mergeCells('A3:F3');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['size' => 10, 'italic' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Statistics
        $sheet->setCellValue('A5', 'STATISTIK UTAMA');
        $sheet->mergeCells('A5:F5');
        $sheet->getStyle('A5')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '2563EB']]
        ]);

        // Stats headers
        $statsHeaders = ['Metrik', 'Nilai', 'Persentase', 'Status'];
        $col = 'A';
        foreach ($statsHeaders as $header) {
            $sheet->setCellValue($col.'6', $header);
            $sheet->getStyle($col.'6')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '374151']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F3F4F6']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);
            $col++;
        }

        // Stats data
        $totalRate = $data['totalPeminjaman'] > 0 ? ($data['totalPengembalian'] / $data['totalPeminjaman']) * 100 : 0;
        $lowStockCount = $data['barangMenipis']->count();
        $lateCount = $data['peminjamanTerlambat']->count();

        $statsData = [
            ['Total Peminjaman', $data['totalPeminjaman'], '-', 'Normal'],
            ['Total Pengembalian', $data['totalPengembalian'], number_format($totalRate, 1) . '%', $totalRate >= 90 ? 'Baik' : ($totalRate >= 70 ? 'Sedang' : 'Perlu Perhatian')],
            ['Jenis Barang', $data['totalBarang'], '-', 'Normal'],
            ['Total Stok', $data['totalStok'], '-', 'Normal'],
            ['Stok Menipis', $lowStockCount, '-', $lowStockCount == 0 ? 'Aman' : 'Perlu Perhatian'],
            ['Keterlambatan', $lateCount, '-', $lateCount == 0 ? 'Baik' : 'Perlu Tindakan']
        ];

        $row = 7;
        foreach ($statsData as $statRow) {
            $col = 'A';
            foreach ($statRow as $value) {
                $sheet->setCellValue($col.$row, $value);
                $col++;
            }

            // Color coding for status
            $statusColor = 'FFFFFF';
            switch ($statRow[3]) {
                case 'Baik':
                case 'Aman':
                    $statusColor = 'DCFCE7';
                    break;
                case 'Sedang':
                case 'Perlu Perhatian':
                    $statusColor = 'FEF3C7';
                    break;
                case 'Perlu Tindakan':
                    $statusColor = 'FEF2F2';
                    break;
            }

            $sheet->getStyle('D'.$row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $statusColor]],
                'font' => ['bold' => true]
            ]);

            $sheet->getStyle('A'.$row.':D'.$row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);
            $row++;
        }

        // Trend Analysis (7 days)
        $sheet->setCellValue('A14', 'TREN PEMINJAMAN 7 HARI TERAKHIR');
        $sheet->mergeCells('A14:F14');
        $sheet->getStyle('A14')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '059669']]
        ]);

        // Trend headers
        $sheet->setCellValue('A15', 'Tanggal');
        $sheet->setCellValue('B15', 'Jumlah Peminjaman');
        $sheet->getStyle('A15:B15')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F3F4F6']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $row = 16;
        foreach ($data['grafikPeminjaman'] as $trendData) {
            $sheet->setCellValue('A'.$row, $trendData['tanggal']);
            $sheet->setCellValue('B'.$row, $trendData['jumlah']);
            $sheet->getStyle('A'.$row.':B'.$row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);
            $row++;
        }

        // Auto-fit columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function createItemsSheet($sheet, $data)
    {
        // Header
        $sheet->setCellValue('A1', 'BARANG PALING DIMINATI');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D97706']]
        ]);

        // Headers
        $headers = ['Rank', 'Nama Barang', 'Kategori', 'Stok Saat Ini', 'Total Peminjaman', 'Total Unit Dipinjam', 'Rata-rata per Peminjaman', 'Status Stok'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col.'3', $header);
            $sheet->getStyle($col.'3')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '374151']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THICK]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);
            $col++;
        }

        // Data
        $row = 4;
        foreach ($data['barangSering'] as $index => $item) {
            $rank = $index + 1;
            $rankText = $rank == 1 ? '🥇 #1' : ($rank == 2 ? '🥈 #2' : ($rank == 3 ? '🥉 #3' : '#'.$rank));

            $sheet->setCellValue('A'.$row, $rankText);
            $sheet->setCellValue('B'.$row, $item->nama_barang);
            $sheet->setCellValue('C'.$row, $item->nama_kategori);
            $sheet->setCellValue('D'.$row, $item->stok);
            $sheet->setCellValue('E'.$row, $item->total_peminjaman);
            $sheet->setCellValue('F'.$row, $item->total_jumlah_dipinjam);
            $sheet->setCellValue('G'.$row, number_format($item->rata_rata_per_peminjaman, 1));
            $sheet->setCellValue('H'.$row, $item->stok <= 5 ? 'MENIPIS' : 'AMAN');

            // Color coding for rank
            $rankColor = 'FFFFFF';
            if ($rank == 1) $rankColor = 'FFD700';
            elseif ($rank == 2) $rankColor = 'C0C0C0';
            elseif ($rank == 3) $rankColor = 'CD7F32';

            $sheet->getStyle('A'.$row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $rankColor]],
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);

            // Color coding for stock status
            $stockColor = $item->stok <= 5 ? 'FEF2F2' : 'DCFCE7';
            $sheet->getStyle('H'.$row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $stockColor]],
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);

            $sheet->getStyle('A'.$row.':H'.$row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);

            $row++;
        }

        // Auto-fit columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function createUsersSheet($sheet, $data)
    {
        // Header
        $sheet->setCellValue('A1', 'ANALISIS PENGGUNA AKTIF');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '2563EB']]
        ]);

        // Top Students Section
        $sheet->setCellValue('A3', 'TOP 10 SISWA PALING AKTIF');
        $sheet->mergeCells('A3:C3');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '059669']]
        ]);

        $sheet->setCellValue('A4', 'Nama Siswa');
        $sheet->setCellValue('B4', 'Kelas');
        $sheet->setCellValue('C4', 'Total Peminjaman');

        $sheet->getStyle('A4:C4')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F3F4F6']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $row = 5;
        foreach ($data['siswaTerbanyak']->take(10) as $siswa) {
            $sheet->setCellValue('A'.$row, $siswa->nama_siswa);
            $sheet->setCellValue('B'.$row, $siswa->kelas);
            $sheet->setCellValue('C'.$row, $siswa->total_peminjaman);

            $sheet->getStyle('A'.$row.':C'.$row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);
            $row++;
        }

        // Top Teachers Section
        $startRow = $row + 2;
        $sheet->setCellValue('E3', 'TOP 10 GURU PALING AKTIF');
        $sheet->mergeCells('E3:G3');
        $sheet->getStyle('E3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D97706']]
        ]);

        $sheet->setCellValue('E4', 'Nama Guru');
        $sheet->setCellValue('F4', 'Mata Pelajaran');
        $sheet->setCellValue('G4', 'Total Peminjaman');

        $sheet->getStyle('E4:G4')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F3F4F6']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $row = 5;
        foreach ($data['guruTerbanyak']->take(10) as $guru) {
            $sheet->setCellValue('E'.$row, $guru->nama_guru);
            $sheet->setCellValue('F'.$row, $guru->nama_mapel);
            $sheet->setCellValue('G'.$row, $guru->total_peminjaman);

            $sheet->getStyle('E'.$row.':G'.$row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);
            $row++;
        }

        // Room Usage Section
        $startRow = max($row, 15) + 2;
        $sheet->setCellValue('A'.$startRow, 'PENGGUNAAN RUANGAN');
        $sheet->mergeCells('A'.$startRow.':B'.$startRow);
        $sheet->getStyle('A'.$startRow)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '0891B2']]
        ]);

        $startRow++;
        $sheet->setCellValue('A'.$startRow, 'Nama Ruangan');
        $sheet->setCellValue('B'.$startRow, 'Frekuensi Penggunaan');
        $sheet->getStyle('A'.$startRow.':B'.$startRow)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F3F4F6']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $row = $startRow + 1;
        foreach ($data['ruanganTerbanyak'] as $ruangan) {
            $sheet->setCellValue('A'.$row, $ruangan->nama_ruangan);
            $sheet->setCellValue('B'.$row, $ruangan->total_penggunaan);

            $sheet->getStyle('A'.$row.':B'.$row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);
            $row++;
        }

        // Auto-fit columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function createAlertsSheet($sheet, $data)
    {
        // Header
        $sheet->setCellValue('A1', 'SISTEM PERINGATAN');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'DC2626']]
        ]);

        // Low Stock Alert
        $sheet->setCellValue('A3', 'PERINGATAN STOK MENIPIS (≤ 5 unit)');
        $sheet->mergeCells('A3:D3');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D97706']]
        ]);

        if ($data['barangMenipis']->count() > 0) {
            $headers = ['Nama Barang', 'Kategori', 'Stok Tersisa', 'Status'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col.'4', $header);
                $sheet->getStyle($col.'4')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FEF3C7']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
                ]);
                $col++;
            }

            $row = 5;
            foreach ($data['barangMenipis'] as $barang) {
                $sheet->setCellValue('A'.$row, $barang->nama_barang);
                $sheet->setCellValue('B'.$row, $barang->kategori->nama_kategori);
                $sheet->setCellValue('C'.$row, $barang->stok);
                $sheet->setCellValue('D'.$row, $barang->stok <= 2 ? 'KRITIS' : 'MENIPIS');

                $statusColor = $barang->stok <= 2 ? 'FEF2F2' : 'FEF3C7';
                $sheet->getStyle('D'.$row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $statusColor]],
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                $sheet->getStyle('A'.$row.':D'.$row)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
                ]);
                $row++;
            }
        } else {
            $sheet->setCellValue('A4', '✅ Semua stok dalam kondisi aman');
            $sheet->mergeCells('A4:D4');
            $sheet->getStyle('A4')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '059669']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'DCFCE7']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);
            $row = 5;
        }

        // Late Returns Alert
        $startRow = $row + 2;
        $sheet->setCellValue('A'.$startRow, 'PERINGATAN KETERLAMBATAN');
        $sheet->mergeCells('A'.$startRow.':F'.$startRow);
        $sheet->getStyle('A'.$startRow)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'DC2626']]
        ]);

        if ($data['peminjamanTerlambat']->count() > 0) {
            $startRow++;
            $headers = ['Peminjam', 'Tipe', 'Barang', 'Tanggal Kembali', 'Keterlambatan', 'Sanksi'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col.$startRow, $header);
                $sheet->getStyle($col.$startRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FEF2F2']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
                ]);
                $col++;
            }

            $row = $startRow + 1;
            foreach ($data['peminjamanTerlambat'] as $late) {
                $peminjam = '';
                $tipe = '';
                if ($late->peminjaman->siswa) {
                    $peminjam = $late->peminjaman->siswa->nama_siswa;
                    $tipe = 'Siswa (' . $late->peminjaman->siswa->kelas . ')';
                } elseif ($late->peminjaman->guru) {
                    $peminjam = $late->peminjaman->guru->nama_guru;
                    $tipe = 'Guru';
                }

                $barang = $late->peminjaman->detail->pluck('barang.nama_barang')->join(', ');

                $sheet->setCellValue('A'.$row, $peminjam);
                $sheet->setCellValue('B'.$row, $tipe);
                $sheet->setCellValue('C'.$row, $barang);
                $sheet->setCellValue('D'.$row, Carbon::parse($late->tanggal_pengembalian)->format('d/m/Y'));
                $sheet->setCellValue('E'.$row, $late->keterlambatan ?? '-');
                $sheet->setCellValue('F'.$row, $late->sanksi);

                $sheet->getStyle('A'.$row.':F'.$row)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
                ]);

                // Highlight critical delays
                if (strpos($late->keterlambatan ?? '', '>7') !== false) {
                    $sheet->getStyle('E'.$row)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FEF2F2']],
                        'font' => ['bold' => true, 'color' => ['rgb' => 'DC2626']]
                    ]);
                }

                $row++;
            }
        } else {
            $startRow++;
            $sheet->setCellValue('A'.$startRow, '✅ Tidak ada peminjaman terlambat');
            $sheet->mergeCells('A'.$startRow.':F'.$startRow);
            $sheet->getStyle('A'.$startRow)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '059669']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'DCFCE7']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);
        }

        // Summary & Recommendations
        $summaryRow = $row + 3;
        $sheet->setCellValue('A'.$summaryRow, 'RINGKASAN & REKOMENDASI');
        $sheet->mergeCells('A'.$summaryRow.':F'.$summaryRow);
        $sheet->getStyle('A'.$summaryRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '059669']]
        ]);

        $summaryRow++;
        $recommendations = [
            'Total peminjaman: ' . number_format($data['totalPeminjaman']) . ' transaksi',
            'Tingkat pengembalian: ' . ($data['totalPeminjaman'] > 0 ? number_format(($data['totalPengembalian']/$data['totalPeminjaman'])*100, 1) : 0) . '%'
        ];

        if ($data['barangSering']->count() > 0) {
            $recommendations[] = 'Barang terpopuler: ' . $data['barangSering']->first()->nama_barang;
        }

        if ($data['barangMenipis']->count() > 0) {
            $recommendations[] = 'URGENT: ' . $data['barangMenipis']->count() . ' item stok menipis';
        }

        if ($data['peminjamanTerlambat']->count() > 0) {
            $recommendations[] = 'PERHATIAN: ' . $data['peminjamanTerlambat']->count() . ' peminjaman terlambat';
        }

        $recommendations = array_merge($recommendations, [
            '',
            'REKOMENDASI TINDAKAN:',
            '📦 Lakukan restocking untuk barang dengan stok menipis',
            '📢 Kirim reminder otomatis untuk mengurangi keterlambatan',
            '📊 Monitor tren peminjaman untuk optimasi stok',
            '🎯 Berikan reward untuk pengembalian tepat waktu',
            '📈 Evaluasi berkala sistem peminjaman'
        ]);

        foreach ($recommendations as $rec) {
            $sheet->setCellValue('A'.$summaryRow, $rec);

            if (strpos($rec, 'URGENT:') !== false || strpos($rec, 'PERHATIAN:') !== false) {
                $sheet->getStyle('A'.$summaryRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'DC2626']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FEF2F2']]
                ]);
            } elseif (strpos($rec, 'REKOMENDASI') !== false) {
                $sheet->getStyle('A'.$summaryRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '059669']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'DCFCE7']]
                ]);
            } elseif (strpos($rec, '📦') !== false || strpos($rec, '📢') !== false || strpos($rec, '📊') !== false || strpos($rec, '🎯') !== false || strpos($rec, '📈') !== false) {
                $sheet->getStyle('A'.$summaryRow)->applyFromArray([
                    'font' => ['italic' => true, 'color' => ['rgb' => '374151']]
                ]);
            }

            if ($rec !== '') {
                $sheet->mergeCells('A'.$summaryRow.':F'.$summaryRow);
            }
            $summaryRow++;
        }

        // Auto-fit columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    // Helper method to get all data consistently
    private function getData(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->get('end_date', now()->format('Y-m-d'));

          $queryCondition = function ($query) use ($startDate, $endDate) {
              if ($startDate && $endDate) {
                  $query->whereBetween('tanggal_pengembalian', [$startDate, $endDate]);
              }
          };


        // Barang sering dipinjam
        $barangSering = DB::table('tbl_detail_pinjam as dp')
            ->join('tbl_peminjaman as p', 'dp.id_pinjam', '=', 'p.id_pinjam')
            ->join('tbl_pengembalian as kmb', 'p.id_pinjam', '=', 'kmb.id_pinjam')
            ->join('tbl_barang as b', 'dp.id_barang', '=', 'b.id_barang')
            ->join('tbl_kategori_barang as k', 'b.id_kategori', '=', 'k.id_kategori')
            ->whereBetween('kmb.tanggal_pengembalian', [$startDate, $endDate])
            ->select(
                'b.nama_barang',
                'k.nama_kategori',
                'b.stok',
                DB::raw('COUNT(dp.id_pinjam) as jumlah_pinjam'),
                DB::raw('SUM(dp.jumlah) as total_jumlah_dipinjam'),
                DB::raw('ROUND(AVG(dp.jumlah),1) as rata_rata_per_peminjaman')
            )
            ->groupBy('b.id_barang', 'b.nama_barang', 'k.nama_kategori', 'b.stok')
            ->orderByDesc('jumlah_pinjam')
            ->get();

        // Statistik status peminjaman
        $statusStatistik = DB::table('tbl_peminjaman as p')
            ->join('tbl_pengembalian as kmb','p.id_pinjam','=','kmb.id_pinjam')
            ->whereBetween('kmb.tanggal_pengembalian', [$startDate, $endDate])
            ->select('p.status', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('p.status')
            ->pluck('jumlah','p.status');

        // Siswa terbanyak
        $siswaTerbanyak = DB::table('tbl_peminjaman as p')
            ->join('tbl_siswa as s','p.id_siswa','=','s.id_siswa')
            ->join('tbl_pengembalian as kmb','p.id_pinjam','=','kmb.id_pinjam')
            ->whereBetween('kmb.tanggal_pengembalian', [$startDate, $endDate])
            ->whereNotNull('p.id_siswa')
            ->select('s.nama_siswa','s.kelas', DB::raw('COUNT(*) as total_peminjaman'))
            ->groupBy('s.id_siswa','s.nama_siswa','s.kelas')
            ->orderBy('total_peminjaman','desc')
            ->get();

        // Guru terbanyak
        $guruTerbanyak = DB::table('tbl_peminjaman as p')
            ->join('tbl_guru as g','p.id_guru','=','g.id_guru')
            ->join('tbl_mapel as m','p.id_mapel','=','m.id_mapel')
            ->join('tbl_pengembalian as kmb','p.id_pinjam','=','kmb.id_pinjam')
            ->whereBetween('kmb.tanggal_pengembalian', [$startDate, $endDate])
            ->whereNotNull('p.id_guru')
            ->select(
                'g.nama_guru',
                'm.nama_mapel',
                DB::raw('COUNT(*) as total_peminjaman')
            )
            ->groupBy('g.id_guru','g.nama_guru','m.nama_mapel')
            ->orderBy('total_peminjaman','desc')
            ->get();

        // Ruangan terbanyak
        $ruanganTerbanyak = DB::table('tbl_peminjaman as p')
            ->join('tbl_ruangan as r','p.id_ruangan','=','r.id_ruangan')
            ->join('tbl_pengembalian as kmb','p.id_pinjam','=','kmb.id_pinjam')
            ->whereBetween('kmb.tanggal_pengembalian', [$startDate, $endDate])
            ->select('r.nama_ruangan', DB::raw('COUNT(*) as total_penggunaan'))
            ->groupBy('r.id_ruangan','r.nama_ruangan')
            ->orderBy('total_penggunaan','desc')
            ->get();

        // Barang stok menipis
        $barangMenipis = Barang::with('kategori')
            ->where('stok','<=',5)
            ->orderBy('stok','asc')
            ->get();

        // Peminjaman terlambat
        $peminjamanTerlambat = Pengembalian::with(['peminjaman.siswa','peminjaman.guru','peminjaman.detail.barang'])
            ->whereNotNull('sanksi')
            ->whereBetween('tanggal_pengembalian', [$startDate, $endDate])
            ->orderBy('tanggal_pengembalian','desc')
            ->get();

        // Ringkasan
        $totalPeminjaman = DB::table('tbl_peminjaman as p')
            ->join('tbl_pengembalian as kmb','p.id_pinjam','=','kmb.id_pinjam')
            ->whereBetween('kmb.tanggal_pengembalian', [$startDate, $endDate])
            ->count();

        $totalPengembalian = Pengembalian::whereBetween('tanggal_pengembalian', [$startDate, $endDate])->count();
        $totalBarang = Barang::count();
        $totalStok   = Barang::sum('stok');

        // Grafik tren peminjaman per hari
        $grafikPeminjaman = DB::table('tbl_pengembalian')
            ->selectRaw('DATE(tanggal_pengembalian) as tanggal, COUNT(*) as jumlah')
            ->whereBetween('tanggal_pengembalian', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(tanggal_pengembalian)'))
            ->orderBy('tanggal')
            ->get()
            ->map(function ($row) {
                return (object) [
                    'tanggal' => \Carbon\Carbon::parse($row->tanggal)->format('d/m'),
                    'jumlah'  => (int) $row->jumlah,
                ];
            });

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'periodeText' => $this->getPeriodeText($startDate, $endDate), // 🔥 tambahan
            'barangSering' => $barangSering,
            'statusStatistik' => $statusStatistik,
            'siswaTerbanyak' => $siswaTerbanyak,
            'guruTerbanyak' => $guruTerbanyak,
            'ruanganTerbanyak' => $ruanganTerbanyak,
            'barangMenipis' => $barangMenipis,
            'peminjamanTerlambat' => $peminjamanTerlambat,
            'totalPeminjaman' => $totalPeminjaman,
            'totalPengembalian' => $totalPengembalian,
            'totalBarang' => $totalBarang,
            'totalStok' => $totalStok,
            'grafikPeminjaman' => $grafikPeminjaman,
        ];
    }


    // Method tambahan untuk halaman detail barang sering dipinjam
    public function barangSering(Request $request)
    {
        if (!auth()->guard('admin')->check() && !auth()->guard('petugas')->check()) {
            return redirect()->route('login')->with('error', 'Akses ditolak.');
        }

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->get('end_date', now()->format('Y-m-d'));

        // Query barang sering dipinjam dengan informasi lebih lengkap
        $barangSering = DB::table('tbl_detail_pinjam as dp')
            ->join('tbl_peminjaman as p', 'dp.id_pinjam', '=', 'p.id_pinjam')
            ->join('tbl_pengembalian as kmb', 'p.id_pinjam', '=', 'kmb.id_pinjam')
            ->join('tbl_barang as b', 'dp.id_barang', '=', 'b.id_barang')
            ->join('tbl_kategori_barang as k', 'b.id_kategori', '=', 'k.id_kategori')
            ->whereBetween('kmb.tanggal_pengembalian', [$startDate, $endDate])
            ->select(
                'b.nama_barang',
                'b.spesifikasi',
                'k.nama_kategori',
                'b.stok',
                DB::raw('COUNT(dp.id_pinjam) as total_peminjaman'),
                DB::raw('SUM(dp.jumlah) as total_jumlah_dipinjam'),
                DB::raw('ROUND(AVG(dp.jumlah),1) as rata_rata_per_peminjaman'),
                DB::raw('MAX(kmb.tanggal_pengembalian) as terakhir_dipinjam')
            )
            ->groupBy('b.id_barang', 'b.nama_barang', 'b.spesifikasi', 'k.nama_kategori', 'b.stok')
            ->orderBy('total_peminjaman','desc')
            ->get();

        $data = compact('barangSering', 'startDate', 'endDate');

        if(auth()->guard('admin')->check()){
            return view('admin.laporan.barang-sering', $data);
        }else{
            return view('petugas.laporan.barang-sering', $data);
        }
    }

    public function index(Request $request)
    {
        if (!auth()->guard('admin')->check() && !auth()->guard('petugas')->check()) {
            return redirect()->route('login')->with('error', 'Akses ditolak.');
        }

        $data = $this->getData($request);

        if(auth()->guard('admin')->check()){
            return view('admin.laporan.index', compact('data'));
        }else{
            return view('petugas.laporan.index', compact('data'));
        }
    }

    private function getPeriodeText($startDate, $endDate)
{
    if ($startDate && $endDate) {
        return Carbon::parse($startDate)->translatedFormat('d F Y') .
               ' - ' .
               Carbon::parse($endDate)->translatedFormat('d F Y');
    }
    return 'Semua Data';
}
}
