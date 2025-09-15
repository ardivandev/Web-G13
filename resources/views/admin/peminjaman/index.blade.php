@extends('layouts.admin.app')

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

    /* Pastikan tombol terlihat */
    .btn-group form {
        display: inline-block !important;
    }

    .btn-group .btn {
        margin-right: 5px;
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

<h1 class="h3 mb-4 text-gray-800">Data Peminjaman</h1>

<div class="card shadow mb-4 bg-white">
    <div class="card-body">
        <div class="card shadow mb-4 bg-white px-3">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <form action="{{ route('admin.peminjaman.index') }}" method="GET" class="form-inline">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm mr-2" placeholder="Cari peminjaman">
                    <button type="submit" class="btn btn-sm btn-primary">Cari</button>
                </form>
            </div>

            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Peminjam</th>
                            <th>Mapel</th>
                            <th>Ruangan</th>
                            <th>Mulai KBM</th>
                            <th>Selesai KBM</th>
                            <th>Status</th>
                            {{-- <th>Aksi</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($peminjaman as $i => $p)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    @if ($p->role === 'SISWA')
                                        {{ $p->siswa->nama_siswa ?? 'Tidak ada nama' }}
                                        <small class="text-muted d-block">Siswa</small>
                                    @elseif ($p->role === 'GURU')
                                        {{ $p->guru->nama_guru ?? 'Tidak ada nama' }}
                                        <small class="text-muted d-block">Guru</small>
                                    @else
                                        {{ $p->role }}
                                    @endif
                                </td>
                                <td>{{ $p->mapel->nama_mapel ?? '-' }}</td>
                                <td>{{ $p->ruangan->nama_ruangan ?? '-' }}</td>
                                <td>
                                    @if($p->mulai_kbm)
                                        {{ \Carbon\Carbon::parse($p->mulai_kbm)->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($p->selesai_kbm)
                                        {{ \Carbon\Carbon::parse($p->selesai_kbm)->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                {{-- Kolom Status --}}
                                <td>
                                    @php
                                        $currentStatus = strtolower($p->status ?? 'menunggu');
                                    @endphp

                                    <span class="badge status-badge
                                        @if($currentStatus == 'menunggu') bg-warning
                                        @elseif($currentStatus == 'dipinjam') bg-success
                                        @elseif($currentStatus == 'ditolak') bg-danger
                                        @elseif($currentStatus == 'selesai') bg-info
                                        @else bg-secondary
                                        @endif">
                                        <i class="fas
                                            @if($currentStatus == 'menunggu') fa-clock
                                            @elseif($currentStatus == 'dipinjam') fa-check
                                            @elseif($currentStatus == 'ditolak') fa-times
                                            @elseif($currentStatus == 'selesai') fa-check-circle
                                            @else fa-question
                                            @endif"></i>
                                        {{ ucfirst($currentStatus) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                    Tidak ada data peminjaman
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
    // Hilangkan alert setelah 5 detik (5000 ms)
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

    // Debug: Log semua data peminjaman untuk troubleshooting
    @if(config('app.debug'))
        console.log('Data Peminjaman:', @json($peminjaman));
    @endif
</script>

@endsection
