<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Gudang Peminjaman</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #2d3748;
            margin: 15px;
            background-color: #ffffff;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding: 20px;
            background-color: #565477;
            border-radius: 8px;
            color: white;
        }

        .header h1 {
            font-size: 22px;
            font-weight: bold;
            color: white;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header h2 {
            font-size: 16px;
            font-weight: bold;
            color: #f8fafc;
            margin-bottom: 5px;
        }

        .header .subtitle {
            font-size: 10px;
            color: #e2e8f0;
        }

        .periode-section {
            background-color: #f8fafc;
            padding: 18px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 2px solid #565477;
            text-align: center;
        }

        .periode-section h3 {
            color: #565477;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .periode-info {
            font-size: 12px;
            color: #4a5568;
            font-weight: 500;
        }

        .debug-info {
            background: #ffeb3b;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            font-size: 9px;
            border: 1px solid #ff9800;
        }

        .stats-container {
            margin-bottom: 25px;
        }

        .stats-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .stats-row {
            display: table-row;
        }

        .stats-cell {
            display: table-cell;
            width: 25%;
            padding: 18px 12px;
            text-align: center;
            border: 2px solid #565477;
            background-color: #f8fafc;
            vertical-align: middle;
        }

        .stats-cell:first-child {
            border-radius: 8px 0 0 8px;
        }

        .stats-cell:last-child {
            border-radius: 0 8px 8px 0;
        }

        .stats-cell .value {
            font-size: 24px;
            font-weight: bold;
            color: #565477;
            margin-bottom: 5px;
            display: block;
        }

        .stats-cell .label {
            font-size: 9px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: white;
            margin-bottom: 12px;
            padding: 12px 15px;
            background-color: #565477;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Tabel Utama */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9px;
            background-color: white;
        }

        table th {
            background-color: #565477;
            border: 1px solid #565477;
            padding: 10px 8px;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            color: white;
            text-align: center;
            letter-spacing: 0.3px;
        }

        table td {
            border: 1px solid #e2e8f0;
            padding: 8px 6px;
            font-size: 9px;
            vertical-align: middle;
            text-align: center;
        }

        table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* Badge dan Status */
        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            color: white;
        }

        .badge-guru {
            background-color: #10b981;
        }
        .badge-siswa {
            background-color: #3b82f6;
        }
        .badge-success {
            background-color: #10b981;
        }
        .badge-danger {
            background-color: #ef4444;
        }
        .badge-warning {
            background-color: #f59e0b;
        }
        .badge-info {
            background-color: #06b6d4;
        }
        .badge-primary {
            background-color: #565477;
        }

        /* Status Peminjaman */
        .status-dipinjam {
            background-color: #f59e0b;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 8px;
        }

        .status-selesai {
            background-color: #10b981;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 8px;
        }

        /* Ranking */
        .rank-1 {
            background-color: #ffd700;
            color: #92400e;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 8px;
            border: 2px solid #d97706;
        }

        .rank-2 {
            background-color: #c0c0c0;
            color: #374151;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 8px;
            border: 2px solid #6b7280;
        }

        .rank-3 {
            background-color: #cd7f32;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 8px;
            border: 2px solid #92400e;
        }

        /* Layout dua kolom */
        .two-column {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 15px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .column:first-child {
            border-radius: 8px 0 0 8px;
            border-right: none;
        }

        .column:last-child {
            border-radius: 0 8px 8px 0;
        }

        .column h4 {
            margin-bottom: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .list-item {
            display: table;
            width: 100%;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .list-item:last-child {
            border-bottom: none;
        }

        .list-content {
            display: table-cell;
            vertical-align: middle;
        }

        .list-value {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
            width: 60px;
        }

        /* Alert */
        .alert {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            border-left: 4px solid;
            font-weight: 500;
        }

        .alert-success {
            background-color: #dcfce7;
            border-left-color: #059669;
            color: #059669;
        }

        .alert-warning {
            background-color: #fef3c7;
            border-left-color: #d97706;
            color: #d97706;
        }

        .alert-danger {
            background-color: #fef2f2;
            border-left-color: #dc2626;
            color: #dc2626;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 15px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #718096;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            background-color: white;
        }

        .no-data {
            text-align: center;
            padding: 25px;
            color: #718096;
            font-style: italic;
            font-size: 11px;
            background-color: #f8fafc;
            border: 2px dashed #e2e8f0;
            border-radius: 8px;
        }

        /* Utility classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .text-sm { font-size: 8px; }

        /* Page breaks */
        .page-break {
            page-break-before: always;
        }

        /* Khusus untuk tabel peminjaman detail */
        .detail-table th {
            font-size: 8px;
            padding: 8px 5px;
        }

        .detail-table td {
            font-size: 8px;
            padding: 6px 4px;
        }

        /* Responsive column widths */
        .col-no { width: 8%; }
        .col-role { width: 10%; }
        .col-nama { width: 20%; }
        .col-mapel { width: 15%; }
        .col-ruangan { width: 12%; }
        .col-barang { width: 15%; }
        .col-waktu { width: 10%; }
        .col-status { width: 10%; }

        /* Summary Section */
        .summary-box {
            padding: 20px;
            background-color: #f8fafc;
            border-radius: 8px;
            border: 2px solid #565477;
        }

        .summary-box h4 {
            margin-bottom: 12px;
            font-size: 12px;
            font-weight: bold;
            color: #565477;
        }

        .summary-box ul {
            margin-left: 20px;
            line-height: 1.8;
            font-size: 10px;
        }

        .summary-box li {
            margin-bottom: 5px;
        }

        /* Colors for different sections */
        .blue-title { color: #3b82f6; }
        .green-title { color: #10b981; }
        .cyan-title { color: #06b6d4; }
        .red-title { color: #ef4444; }

        @page {
            margin: 20mm 15mm 25mm 15mm;
        }

        @media print {
            .footer {
                position: fixed;
                bottom: 0;
            }

            body {
                background: white;
            }

            .debug-info {
                display: none;
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .stats-grid {
                display: block;
            }

            .stats-cell {
                display: block;
                width: 100%;
                margin-bottom: 10px;
                border-radius: 8px;
            }

            .two-column {
                display: block;
            }

            .column {
                display: block;
                width: 100%;
                margin-bottom: 15px;
                border-radius: 8px;
            }
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <h1>SISTEM INFORMASI GUDANG</h1>
        <h2>LAPORAN PEMINJAMAN BARANG</h2>
        <div class="subtitle">Generated pada {{ now()->timezone('Asia/Jakarta')->format('d F Y, H:i') }} WIB</div>
    </div>

    {{-- PERBAIKAN: Periode Section --}}
    <div class="periode-section">
        <h3>PERIODE LAPORAN</h3>
        <div class="periode-info">
            @if($startDate && $endDate)
                <strong>{{ \Carbon\Carbon::parse($startDate)->timezone('Asia/Jakarta')->translatedFormat('d F Y') }}</strong>
                sampai dengan
                <strong>{{ \Carbon\Carbon::parse($endDate)->timezone('Asia/Jakarta')->translatedFormat('d F Y') }}</strong>
                <br>
                <small>({{ \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1 }} hari)</small>
            @else
                <strong>SEMUA DATA YANG TERSEDIA</strong>
                <br>
                <small>Tidak ada filter tanggal yang diterapkan</small>
            @endif
        </div>
    </div>

    {{-- Statistics Overview --}}
    <div class="section">
        <div class="section-title">
            RINGKASAN STATISTIK PEMINJAMAN
        </div>
        <div class="stats-container">
            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stats-cell">
                        <span class="value">{{ number_format($totalPeminjaman) }}</span>
                        <span class="label">Total Peminjaman</span>
                    </div>
                    <div class="stats-cell">
                        <span class="value">{{ number_format($totalPengembalian) }}</span>
                        <span class="label">Total Pengembalian</span>
                    </div>
                    <div class="stats-cell">
                        <span class="value">{{ number_format($totalBarang) }}</span>
                        <span class="label">Jenis Barang</span>
                    </div>
                    <div class="stats-cell">
                        <span class="value">{{ number_format($totalStok) }}</span>
                        <span class="label">Total Stok</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Peminjaman Table --}}
    <div class="section">
        <div class="section-title">
            DETAIL TRANSAKSI PEMINJAMAN
        </div>
        @if(isset($detailPeminjaman) && $detailPeminjaman->count() > 0)
        <table class="detail-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th class="col-role">Role</th>
                    <th class="col-nama">Nama Peminjam</th>
                    <th class="col-mapel">Mapel</th>
                    <th class="col-ruangan">Ruangan</th>
                    <th class="col-barang">Barang</th>
                    <th class="col-waktu">Mulai KBM</th>
                    <th class="col-waktu">Selesai KBM</th>
                    <th class="col-status">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detailPeminjaman as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">
                        @if($item->siswa)
                            <span class="badge badge-siswa">SISWA</span>
                        @elseif($item->guru)
                            <span class="badge badge-guru">GURU</span>
                        @endif
                    </td>
                    <td>
                        @if($item->siswa)
                            <strong>{{ $item->siswa->nama_siswa }}</strong><br>
                            <small class="text-sm">{{ $item->siswa->kelas }}</small>
                        @elseif($item->guru)
                            <strong>{{ $item->guru->nama_guru }}</strong><br>
                            <small class="text-sm">Guru</small>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($item->mapel)
                            <span class="badge badge-info">{{ $item->mapel->nama_mapel }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if($item->ruangan)
                            {{ $item->ruangan->nama_ruangan }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-sm">
                        @foreach($item->detail as $detail)
                            • {{ $detail->barang->nama_barang }}
                            @if($detail->jumlah > 1)
                                ({{ $detail->jumlah }})
                            @endif
                            <br>
                        @endforeach
                    </td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                    </td>
                    <td class="text-center">
                        @if($item->pengembalian)
                            {{ \Carbon\Carbon::parse($item->pengembalian->tanggal_pengembalian)->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if($item->status == 'selesai' || $item->pengembalian)
                            <span class="status-selesai">Selesai</span>
                        @else
                            <span class="status-dipinjam">Dipinjam</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="no-data">
            Tidak ada data peminjaman dalam periode yang dipilih
        </div>
        @endif
    </div>

    {{-- Top Items --}}
    <div class="section">
        <div class="section-title">
            BARANG PALING DIMINATI
        </div>
        @if($barangSering->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">Rank</th>
                    <th style="width: 30%;">Nama Barang</th>
                    <th style="width: 15%;">Kategori</th>
                    <th style="width: 10%;">Stok</th>
                    <th style="width: 12%;">Frekuensi</th>
                    <th style="width: 12%;">Total Unit</th>
                    <th style="width: 11%;">Rata-rata</th>
                </tr>
            </thead>
            <tbody>
                @foreach($barangSering->take(10) as $index => $item)
                <tr>
                    <td class="text-center">
                        @if($index == 0)
                            <span class="rank-1">#1</span>
                        @elseif($index == 1)
                            <span class="rank-2">#2</span>
                        @elseif($index == 2)
                            <span class="rank-3">#3</span>
                        @else
                            <span class="badge badge-primary">#{{ $index + 1 }}</span>
                        @endif
                    </td>
                    <td class="font-bold">{{ $item->nama_barang }}</td>
                    <td class="text-center">
                        <span class="badge badge-info">{{ $item->nama_kategori }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $item->stok <= 5 ? 'badge-danger' : 'badge-success' }}">
                            {{ $item->stok }}
                        </span>
                    </td>
                    <td class="text-center font-bold">{{ $item->jumlah_pinjam }}x</td>
                    <td class="text-center">{{ $item->total_jumlah_dipinjam }} unit</td>
                    <td class="text-center">{{ number_format($item->rata_rata_per_peminjaman, 1) }} unit</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="no-data">
            Tidak ada data peminjaman dalam periode yang dipilih
        </div>
        @endif
    </div>

    {{-- User Analytics --}}
    <div class="section">
        <div class="section-title">
            ANALISIS PENGGUNA AKTIF
        </div>
        <div class="two-column">
            <div class="column">
                <h4 class="blue-title">Top 5 Siswa Aktif</h4>
                @if($siswaTerbanyak->count() > 0)
                    @foreach($siswaTerbanyak->take(5) as $index => $siswa)
                    <div class="list-item">
                        <div class="list-content">
                            <strong>{{ $siswa->nama_siswa }}</strong><br>
                            <span class="text-sm" style="color: #718096;">{{ $siswa->kelas }}</span>
                        </div>
                        <div class="list-value">
                            <span class="badge badge-primary">{{ $siswa->total_peminjaman }}</span>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="no-data text-sm">Tidak ada data siswa</div>
                @endif
            </div>
            <div class="column">
                <h4 class="green-title">Top 5 Guru Aktif</h4>
                @if($guruTerbanyak->count() > 0)
                    @foreach($guruTerbanyak->take(5) as $index => $guru)
                    <div class="list-item">
                        <div class="list-content">
                            <strong>{{ $guru->nama_guru }}</strong><br>
                            <span class="text-sm" style="color: #718096;">{{ $guru->nama_mapel }}</span>
                        </div>
                        <div class="list-value">
                            <span class="badge badge-success">{{ $guru->total_peminjaman }}</span>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="no-data text-sm">Tidak ada data guru</div>
                @endif
            </div>
        </div>
    </div>

    <div class="page-break"></div>

    {{-- Room Usage & Stock Alert --}}
    <div class="section">
        <div class="section-title">
            UTILITAS RUANGAN & PERINGATAN STOK
        </div>
        <div class="two-column">
            <div class="column">
                <h4 class="cyan-title">Ruangan Sering Digunakan</h4>
                @if($ruanganTerbanyak->count() > 0)
                    @foreach($ruanganTerbanyak->take(5) as $ruangan)
                    <div class="list-item">
                        <div class="list-content">{{ $ruangan->nama_ruangan }}</div>
                        <div class="list-value">
                            <span class="badge badge-info">{{ $ruangan->total_penggunaan }}x</span>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="no-data text-sm">Tidak ada data ruangan</div>
                @endif
            </div>
            <div class="column">
                <h4 class="red-title">Peringatan Stok (≤5 unit)</h4>
                @if($barangMenipis->count() > 0)
                    @foreach($barangMenipis->take(5) as $barang)
                    <div class="list-item">
                        <div class="list-content">
                            <strong>{{ $barang->nama_barang }}</strong><br>
                            <span class="text-sm" style="color: #718096;">{{ $barang->kategori->nama_kategori }}</span>
                        </div>
                        <div class="list-value">
                            <span class="badge badge-danger">{{ $barang->stok }} unit</span>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="alert alert-success">
                        <strong>Semua stok dalam kondisi aman</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Late Returns --}}
    @if($peminjamanTerlambat->count() > 0)
    <div class="section">
        <div class="section-title" style="background-color: #ef4444;">
            PERINGATAN KETERLAMBATAN
        </div>
        <div class="alert alert-danger">
            <strong>Perhatian!</strong> Terdapat {{ $peminjamanTerlambat->count() }} peminjaman yang terlambat dikembalikan.
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width: 25%;">Peminjam</th>
                    <th style="width: 30%;">Barang</th>
                    <th style="width: 15%;">Tgl Kembali</th>
                    <th style="width: 15%;">Keterlambatan</th>
                    <th style="width: 15%;">Sanksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($peminjamanTerlambat->take(15) as $late)
                <tr>
                    <td>
                        @if($late->peminjaman->siswa)
                            <strong>{{ $late->peminjaman->siswa->nama_siswa }}</strong><br>
                            <span class="text-sm">{{ $late->peminjaman->siswa->kelas }}</span>
                        @elseif($late->peminjaman->guru)
                            <strong>{{ $late->peminjaman->guru->nama_guru }}</strong><br>
                            <span class="text-sm">Guru</span>
                        @endif
                    </td>
                    <td class="text-sm">
                        @foreach($late->peminjaman->detail as $detail)
                            • {{ $detail->barang->nama_barang }}<br>
                        @endforeach
                    </td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($late->tanggal_pengembalian)->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</td>
                    <td class="text-center">
                        <span class="badge badge-warning">{{ $late->keterlambatan ?? '-' }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-danger">{{ $late->sanksi }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Summary & Recommendations --}}
    <div class="section">
        <div class="section-title">
           RINGKASAN & REKOMENDASI
        </div>
        <div class="summary-box">
            <h4>Kesimpulan Laporan:</h4>
            <ul>
                <li><strong>Periode Laporan:</strong>
                    @if($startDate && $endDate)
                        {{ \Carbon\Carbon::parse($startDate)->timezone('Asia/Jakarta')->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->timezone('Asia/Jakarta')->translatedFormat('d F Y') }}
                    @else
                        Semua data yang tersedia
                    @endif
                </li>
                <li><strong>Total Transaksi Peminjaman:</strong> {{ number_format($totalPeminjaman) }} peminjaman</li>
                <li><strong>Tingkat Pengembalian:</strong> {{ $totalPeminjaman > 0 ? number_format(($totalPengembalian/$totalPeminjaman)*100, 1) : 0 }}%</li>
                @if($barangSering->count() > 0)
                <li><strong>Barang Terpopuler:</strong> {{ $barangSering->first()->nama_barang ?? 'Tidak ada data' }}</li>
                @endif
                @if($barangMenipis->count() > 0)
                <li><strong>Peringatan Stok:</strong> {{ $barangMenipis->count() }} item memerlukan perhatian</li>
                @endif
                @if($peminjamanTerlambat->count() > 0)
                <li><strong>Keterlambatan:</strong> {{ $peminjamanTerlambat->count() }} peminjaman terlambat</li>
                @endif
            </ul>

            <h4 style="margin-top: 18px; color: #10b981;">Rekomendasi Tindakan:</h4>
            <ul>
                @if($barangMenipis->count() > 0)
                <li><strong>Prioritas Tinggi:</strong> Segera lakukan pengadaan untuk {{ $barangMenipis->count() }} item yang stoknya menipis</li>
                @endif
                @if($peminjamanTerlambat->count() > 0)
                <li><strong>Sistem Reminder:</strong> Tingkatkan pengingat otomatis untuk mengurangi keterlambatan ({{ $peminjamanTerlambat->count() }} kasus)</li>
                @endif
                @if($barangSering->count() > 0)
                <li><strong>Optimasi Stok:</strong> Pertimbangkan menambah stok untuk barang yang sering dipinjam</li>
                <li><strong>Evaluasi Berkala:</strong> Lakukan review sistem peminjaman setiap bulan untuk efisiensi</li>
                <li><strong>Program Reward:</strong> Berikan apresiasi kepada pengguna yang tertib dalam pengembalian</li>
                <li><strong>Monitoring Aktif:</strong> Tingkatkan pengawasan pada jam-jam puncak peminjaman</li>
                @endif
            </ul>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div>
            <strong>Laporan Sistem Informasi Gudang</strong> |
            Generated: {{ now()->timezone('Asia/Jakarta')->format('d F Y, H:i') }} WIB
        </div>
    </div>
</body>
</html>
