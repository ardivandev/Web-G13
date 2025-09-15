@extends('layouts.petugas.app')

@section('content')
<style>
    .btn-primary {
        background-color: #565477;
        border-color: #565477;
    }
    .btn-primary:hover {
        background-color: #474163;
        border-color: #474163;
    }
    .btn-primary:active {
        background-color: #474163;
        border-color: #474163;
    }

    .status-badge {
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
        color: white !important;
    }

    .text-danger {
        font-weight: bold;
    }

    .btn-group form {
        display: inline-block !important;
    }

    .btn-group .btn {
        margin-right: 5px;
    }

    .barang-list {
        max-width: 250px;
    }
    .barang-item {
        display: block;
        margin-bottom: 3px;
        padding: 2px 5px;
        background-color: #f8f9fa;
        border-radius: 3px;
        font-size: 0.85rem;
    }
</style>

@if (session('success'))
    <div class="alert alert-success mt-2" id="success-alert">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger mt-2" id="error-alert">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger mt-2" id="validation-error-alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>⚠️ : {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<h1 class="h3 mb-4 text-gray-800">Data Pengembalian</h1>

<div class="card shadow mb-4 bg-white">
    <div class="card-body">
        <div class="card shadow mb-4 bg-white px-3">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <form action="{{ route('admin.pengembalian.index') }}" method="GET" class="form-inline">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm mr-2" placeholder="Cari nama peminjam">
                    <button type="submit" class="btn btn-sm btn-primary">Cari</button>
                </form>
            </div>

            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Peminjam</th>
                            <th>Barang Yang Dipinjam</th>
                            <th>Status Peminjaman</th>
                            <th>Tanggal Harus Kembali</th>
                            <th>Tanggal Pengembalian</th>
                            <th>Status Pengembalian</th>
                            <th>Sanksi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengembalian as $i => $k)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    @if($k->peminjaman->role === 'SISWA' && $k->peminjaman->siswa)
                                        {{ $k->peminjaman->siswa->nama_siswa }}
                                        <small class="text-muted d-block">Siswa</small>
                                    @elseif($k->peminjaman->role === 'GURU' && $k->peminjaman->guru)
                                        {{ $k->peminjaman->guru->nama_guru }}
                                        <small class="text-muted d-block">Guru</small>
                                    @else
                                        <span class="text-muted">Data tidak tersedia</span>
                                    @endif
                                </td>
                                {{-- Kolom Barang - DIPERBAIKI --}}
                                <td class="barang-list">
                                    @if($k->peminjaman->detail && $k->peminjaman->detail->count() > 0)
                                        @foreach($k->peminjaman->detail as $detail)
                                            <span class="barang-item">
                                                {{ $detail->barang->nama_barang ?? 'Unknown' }}
                                                ({{ $detail->jumlah ?? 0 }})
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge status-badge
                                        @if($k->peminjaman->status == 'Dipinjam') bg-success
                                        @elseif($k->peminjaman->status == 'Selesai') bg-info
                                        @elseif($k->peminjaman->status == 'Menunggu') bg-warning
                                        @else bg-secondary
                                        @endif">
                                        <i class="fas
                                            @if($k->peminjaman->status == 'Dipinjam') fa-check
                                            @elseif($k->peminjaman->status == 'Selesai') fa-check-circle
                                            @elseif($k->peminjaman->status == 'Menunggu') fa-clock
                                            @else fa-question
                                            @endif"></i>
                                        {{ ucfirst($k->peminjaman->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($k->tanggal_harus_kembali)
                                        {{ \Carbon\Carbon::parse($k->tanggal_harus_kembali)->format('d/m/Y') }}
                                        @php
                                            $today = \Carbon\Carbon::now();
                                            $dueDate = \Carbon\Carbon::parse($k->tanggal_harus_kembali);
                                            $daysLeft = floor($today->floatDiffInDays($dueDate, false));
                                            $hoursLeft = floor($today->floatDiffInHours($dueDate, false));
                                        @endphp
                                        @if($k->tanggal_pengembalian == null)
                                            @if($daysLeft < 0)
                                                <small class="text-danger d-block">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    Terlambat {{ abs($daysLeft) }} hari
                                                </small>
                                            @elseif($daysLeft == 0)
                                                <small class="text-warning d-block">
                                                    <i class="fas fa-clock"></i>
                                                    Jatuh tempo hari ini
                                                </small>
                                            @elseif($daysLeft < 1)
                                                <small class="text-info d-block">
                                                    <i class="fas fa-info-circle"></i>
                                                    {{ abs($hoursLeft) }} jam lagi
                                                </small>
                                            @else
                                                <small class="text-info d-block">
                                                    <i class="fas fa-info-circle"></i>
                                                    {{ $daysLeft }} hari lagi
                                                </small>
                                            @endif
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($k->tanggal_pengembalian)
                                        {{ \Carbon\Carbon::parse($k->tanggal_pengembalian)->format('d/m/Y') }}
                                        @php
                                            $returnDate = \Carbon\Carbon::parse($k->tanggal_pengembalian);
                                            $diffInDays = floor($returnDate->floatDiffInDays(\Carbon\Carbon::now()));
                                            $diffInHours = floor($returnDate->floatDiffInHours(\Carbon\Carbon::now()));
                                        @endphp
                                        <small class="text-muted d-block">
                                            @if($diffInDays >= 1)
                                                ({{ $diffInDays }} hari yang lalu)
                                            @else
                                                ({{ $diffInHours }} jam yang lalu)
                                            @endif
                                        </small>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-minus-circle"></i>
                                            Belum dikembalikan
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($k->tanggal_pengembalian)
                                        <span class="badge bg-success status-badge">
                                            <i class="fas fa-check"></i> Sudah Dikembalikan
                                        </span>
                                    @else
                                        @if($k->tanggal_harus_kembali)
                                            @php
                                                $today = \Carbon\Carbon::now();
                                                $dueDate = \Carbon\Carbon::parse($k->tanggal_harus_kembali);
                                                $daysLeft = floor($today->floatDiffInDays($dueDate, false));
                                            @endphp
                                            @if($daysLeft < 0)
                                                <span class="badge bg-danger status-badge">
                                                    <i class="fas fa-exclamation-triangle"></i> Terlambat
                                                </span>
                                            @elseif($daysLeft == 0)
                                                <span class="badge bg-warning status-badge">
                                                    <i class="fas fa-clock"></i> Jatuh Tempo
                                                </span>
                                            @else
                                                <span class="badge bg-primary status-badge">
                                                    <i class="fas fa-hourglass-half"></i> Dipinjam
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary status-badge">
                                                <i class="fas fa-question"></i> Data Tidak Lengkap
                                            </span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $sanksiOtomatis = '';

                                        if ($k->tanggal_harus_kembali && !$k->tanggal_pengembalian) {
                                            $today = \Carbon\Carbon::now();
                                            $dueDate = \Carbon\Carbon::parse($k->tanggal_harus_kembali);
                                            $daysLeft = floor($today->floatDiffInDays($dueDate, false));
                                            $daysLate = abs($daysLeft);

                                            if ($daysLeft < 0) {
                                                if ($daysLate <= 3) {
                                                    $sanksiOtomatis = 'Teguran Tertulis';
                                                } elseif ($daysLate <= 7) {
                                                    $sanksiOtomatis = 'Denda Rp ' . number_format($daysLate * 2000, 0, ',', '.');
                                                } else {
                                                    $sanksiOtomatis = 'Suspend 1 Bulan + Denda Rp ' . number_format($daysLate * 5000, 0, ',', '.');
                                                }
                                            }
                                        }

                                        $finalSanksi = $k->sanksi ?: $sanksiOtomatis;
                                    @endphp

                                    @if($finalSanksi)
                                        <span class="text-danger">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $finalSanksi }}
                                        </span>
                                    @else
                                        <span class="text-success">
                                            <i class="fas fa-check-circle"></i>
                                            Tidak ada sanksi
                                        </span>
                                    @endif
                                </td>
                                <td style="min-width: 180px;">
                                    @if($k->peminjaman->status == 'dipinjam' && !$k->tanggal_pengembalian)
                                        <form action="{{ route('petugas.pengembalian.complete', $k->id_kembali) }}"
                                            method="POST"
                                            style="display:inline-block;"
                                            onsubmit="return confirm('Yakin ingin menyelesaikan peminjaman ini?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-info">
                                                <i class="fas fa-check-circle"></i> Selesai
                                            </button>
                                        </form>
                                    @elseif($k->peminjaman->status == 'Selesai')
                                        <span class="badge bg-success status-badge">
                                            <i class="fas fa-check-circle"></i> Telah Selesai
                                        </span>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-minus"></i> Tidak Ada Aksi
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                    Tidak ada data pengembalian
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Hilangkan alert setelah 5 detik
    setTimeout(() => {
        let success = document.getElementById('success-alert');
        let error = document.getElementById('error-alert');
        let validationError = document.getElementById('validation-error-alert');

        [success, error, validationError].forEach(alert => {
            if (alert) {
                alert.style.transition = "opacity 0.5s ease";
                alert.style.opacity = "0";
                setTimeout(() => alert.remove(), 500);
            }
        });
    }, 5000);

    @if(config('app.debug'))
        console.log('Data Pengembalian:', @json($pengembalian));
    @endif
</script>
@endsection
