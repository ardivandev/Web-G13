<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #565477;
            padding-bottom: 15px;
        }

        .header h1 {
            color: #565477;
            font-size: 20px;
            margin: 0;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        .info-section {
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
        }

        .info-row table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-row td {
            padding: 4px 0;
            vertical-align: top;
        }

        .info-row td:first-child {
            width: 150px;
            font-weight: bold;
        }

        .info-row td:nth-child(2) {
            width: 10px;
            text-align: center;
        }

        .badge {
            background-color: #28a745;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            display: inline-block;
        }

        .badge-danger {
            background-color: #dc3545;
        }

        .badge-primary {
            background-color: #007bff;
        }

        .items-section {
            margin-top: 25px;
        }

        .items-title {
            font-size: 14px;
            font-weight: bold;
            color: #565477;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .items-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }

        .items-table td:nth-child(1) {
            width: 60%;
        }

        .items-table td:nth-child(2) {
            width: 20%;
            text-align: center;
        }

        .items-table td:nth-child(3) {
            width: 20%;
            text-align: center;
            font-weight: bold;
        }

        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .summary-row:last-child {
            margin-bottom: 0;
            font-weight: bold;
            font-size: 13px;
        }

        .footer-note {
            margin-top: 30px;
            padding: 15px;
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
            font-size: 11px;
            color: #666;
        }

        .footer-note strong {
            color: #333;
        }

        .generated-info {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BUKTI PEMINJAMAN BARANG GUDANG</h1>
        <p>Tanggal:
           {{ now()->timezone('Asia/Jakarta')->format('d F Y, H:i') }}
            WIB
        </p>
    </div>

    <div class="info-section">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 150px; font-weight: bold; padding: 4px 0;">ID Peminjaman</td>
                <td style="width: 10px; text-align: center;">:</td>
                <td>#{{ $peminjaman->id_pinjam ?? 'N/A' }}</td>
            </tr>

            <tr>
                <td style="width: 150px; font-weight: bold; padding: 4px 0;">Role</td>
                <td style="width: 10px; text-align: center;">:</td>
                <td><span class="badge badge-primary">{{ ucfirst($peminjaman->role) }}</span></td>
            </tr>

            @if($peminjaman->role == 'SISWA' && $peminjaman->siswa)
            <tr>
                <td style="font-weight: bold; padding: 4px 0;">Nama Siswa</td>
                <td style="text-align: center;">:</td>
                <td>{{ $peminjaman->siswa->nama_siswa }}
                    @if(isset($peminjaman->siswa->kelas))
                        <span style="color: #666;">({{ $peminjaman->siswa->kelas }})</span>
                    @endif
                </td>
            </tr>
            @endif

            @if($peminjaman->guru)
            <tr>
                <td style="font-weight: bold; padding: 4px 0;">Nama Guru</td>
                <td style="text-align: center;">:</td>
                <td>{{ $peminjaman->guru->nama_guru }}</td>
            </tr>
            @endif

            <tr>
                <td style="font-weight: bold; padding: 4px 0;">Mata Pelajaran</td>
                <td style="text-align: center;">:</td>
                <td>{{ $peminjaman->mapel->nama_mapel ?? 'N/A' }}</td>
            </tr>

            <tr>
                <td style="font-weight: bold; padding: 4px 0;">Ruangan</td>
                <td style="text-align: center;">:</td>
                <td>{{ $peminjaman->ruangan->nama_ruangan ?? 'N/A' }}</td>
            </tr>

            <tr>
                <td style="font-weight: bold; padding: 4px 0;">No. HP</td>
                <td style="text-align: center;">:</td>
                <td>{{ $peminjaman->no_telp ?? 'N/A' }}</td>
            </tr>

            <tr>
                <td style="font-weight: bold; padding: 4px 0;">Waktu KBM</td>
                <td style="text-align: center;">:</td>
                <td>
                    @if($peminjaman->mulai_kbm)
                        <span class="badge">{{ date('H:i', strtotime($peminjaman->mulai_kbm)) }}</span> -
                        <span class="badge badge-danger">{{ date('H:i', strtotime($peminjaman->selesai_kbm)) }}</span>
                    @else
                        <span class="badge">N/A</span> -
                        <span class="badge badge-danger">N/A</span>
                    @endif
                </td>
            </tr>

            @if($peminjaman->jaminan)
            <tr>
                <td style="font-weight: bold; padding: 4px 0;">Jaminan</td>
                <td style="text-align: center;">:</td>
                <td>{{ $peminjaman->jaminan }}</td>
            </tr>
            @endif

            <tr>
                <td style="font-weight: bold; padding: 4px 0;">Tanggal Peminjaman</td>
                <td style="text-align: center;">:</td>
                <td>
                  {{ now()->timezone('Asia/Jakarta')->format('d F Y, H:i') }}
                    WIB
                </td>
            </tr>
        </table>
    </div>

    <div class="items-section">
        <div class="items-title">Daftar Barang yang Dipinjam</div>

        @if(isset($peminjaman->detail) && count($peminjaman->detail) > 0)
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalQty = 0; @endphp
                    @foreach($peminjaman->detail as $detail)
                        @php $qty = $detail->jumlah ?? 1; $totalQty += $qty; @endphp
                        <tr>
                            <td>{{ $detail->barang->nama_barang ?? 'Barang tidak ditemukan' }}</td>
                            <td>{{ $qty }}</td>
                            <td>{{ $qty }} unit</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="summary-box">
                <div class="summary-row">
                    <span>Total Jenis Barang:</span>
                    <span>{{ count($peminjaman->detail) }} item</span>
                </div>
                <div class="summary-row">
                    <span>Total Quantity:</span>
                    <span>{{ $totalQty }} unit</span>
                </div>
            </div>

        @elseif(isset($cart) && count($cart) > 0)
            {{-- Fallback jika menggunakan session cart --}}
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalQty = 0; @endphp
                    @foreach($cart as $item)
                        @php $qty = $item['jumlah'] ?? 1; $totalQty += $qty; @endphp
                        <tr>
                            <td>{{ $item['nama'] ?? 'N/A' }}</td>
                            <td>{{ $qty }}</td>
                            <td>{{ $qty }} unit</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="summary-box">
                <div class="summary-row">
                    <span>Total Jenis Barang:</span>
                    <span>{{ count($cart) }} item</span>
                </div>
                <div class="summary-row">
                    <span>Total Quantity:</span>
                    <span>{{ $totalQty }} unit</span>
                </div>
            </div>
        @else
            <p style="text-align: center; color: #999; padding: 20px;">
                Tidak ada detail barang yang ditemukan.
            </p>
        @endif
    </div>

    <div class="footer-note">
        <strong>Catatan Penting:</strong><br>
        1. Harap simpan bukti peminjaman ini sebagai referensi<br>
        2. Tunjukkan bukti ini kepada petugas lab saat mengambil barang<br>
        3. Pastikan barang dikembalikan sesuai waktu yang telah ditentukan<br>
        4. Hubungi petugas lab jika ada perubahan jadwal
    </div>

    <div class="generated-info">
        <p>Dokumen ini dibuat otomatis oleh sistem pada
            @php
                // Gunakan waktu Indonesia (WIB)
                $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                echo $now->format('d/m/Y H:i');
            @endphp
            WIB
        </p>
    </div>
</body>
</html>
