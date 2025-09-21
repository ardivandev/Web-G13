@extends('layouts.petugas.app')

@push('styles')
<style>
/* ====== THEME STYLE ====== */
:root {
    --primary: #4e73df;
    --secondary: #6c757d;
    --success: #1cc88a;
    --warning: #f6c23e;
    --danger: #e74a3b;
    --info: #36b9cc;
    --gray: #f8f9fc;
}

.card-slim {
    border: none;
    border-radius: 15px;
    background: #fff;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    transition: 0.2s;
}
.card-slim:hover {
    transform: translateY(-3px);
}

/* Statistic Card */
.stat-box {
    padding: 1.5rem;
    border-radius: 15px;
    color: #fff;
    position: relative;
}
.stat-box h5 {
    font-size: 0.9rem;
    font-weight: 600;
    opacity: 0.9;
}
.stat-box h2 {
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0;
}
.stat-icon {
    font-size: 2rem;
    position: absolute;
    right: 20px;
    bottom: 20px;
    opacity: 0.3;
}

/* Table */
.table-slim thead {
    background: var(--gray);
    font-size: 0.8rem;
    text-transform: uppercase;
    font-weight: 600;
    color: #6c757d;
}
.table-slim td {
    vertical-align: middle;
    padding: 0.8rem 1rem;
}

/* Badge */
.badge-slim {
    border-radius: 30px;
    padding: 0.35rem 0.8rem;
    font-size: 0.75rem;
    font-weight: 600;
}
</style>
@endpush

@section('content')
<!-- Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <h3 class="fw-bold text-dark m-0">
        <i class="fas fa-chart-line me-2 text-primary"></i>Laporan Gudang
    </h3>
    {{-- <div class="d-flex gap-2">
        <a href="{{ route('admin.laporan.export-pdf') }}" class="btn btn-danger btn-sm rounded-3">
            <i class="fas fa-file-pdf me-1"></i> Export PDF
        </a>
    </div> --}}
</div>

<!-- Filter -->
<div class="card-slim mb-4">
    <div class="p-3 border-bottom">
        <h6 class="fw-bold text-primary mb-0">
            <i class="fas fa-filter me-2"></i>Filter Laporan
        </h6>
    </div>
    <div class="p-3">
        <form method="GET" action="{{ route('petugas.laporan.index') }}" class="row g-3">
    <div class="col-lg-4 col-md-6">
        <label class="form-label fw-semibold">
            <i class="fas fa-calendar-alt me-1 text-primary"></i>Tanggal Mulai
        </label>
        <input type="date" name="start_date" class="form-control"
               value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
    </div>
    <div class="col-lg-4 col-md-6">
        <label class="form-label fw-semibold">
            <i class="fas fa-calendar-check me-1 text-primary"></i>Tanggal Selesai
        </label>
        <input type="date" name="end_date" class="form-control"
               value="{{ request('end_date', now()->format('Y-m-d')) }}">
    </div>
    <div class="col-lg-4 col-md-12 d-flex align-items-end">
        <div class="d-flex w-100 gap-2">
            <button type="submit" class="btn btn-primary flex-fill rounded-3 mr-2">
                <i class="fas fa-search me-1"></i>Filter
            </button>
            <a href="{{ route('petugas.laporan.index') }}" class="btn btn-secondary flex-fill rounded-3 mr-2">
                <i class="fas fa-redo me-1"></i>Reset
            </a>
            <button type="submit" formaction="{{ route('petugas.laporan.export-pdf') }}"
                    class="btn btn-danger flex-fill rounded-3">
                <i class="fas fa-file-pdf me-1"></i>PDF
            </button>
        </div>
    </div>
</form>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-box bg-primary card-slim">
            <h5>Total Barang</h5>
            <h2>{{ $data['totalBarang'] }}</h2>
            <i class="fas fa-boxes stat-icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-box bg-success card-slim">
            <h5>Total Stok</h5>
            <h2>{{ $data['totalStok'] }}</h2>
            <i class="fas fa-warehouse stat-icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-box bg-info card-slim">
            <h5>Peminjaman</h5>
            <h2>{{ $data['totalPeminjaman'] }}</h2>
            <i class="fas fa-hand-holding stat-icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-box bg-warning card-slim">
            <h5>Pengembalian</h5>
            <h2>{{ $data['totalPengembalian'] }}</h2>
            <i class="fas fa-undo stat-icon"></i>
        </div>
    </div>
</div>

<!-- Tables -->
<div class="row">
    <!-- Barang Sering Dipinjam -->
    <div class="col-lg-6 mb-4">
        <div class="card-slim h-100">
            <div class="p-3 border-bottom">
                <h6 class="fw-bold text-primary mb-0">
                    <i class="fas fa-fire me-2"></i>Barang Sering Dipinjam
                </h6>
            </div>
            <div class="p-3">
                @if($data['barangSering']->count())
                <table class="table table-slim">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th class="text-center">Dipinjam</th>
                            <th class="text-center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['barangSering'] as $index => $item)
                        <tr>
                            <td>{{ $item->nama_barang }}</td>
                            <td class="text-center text-white">
                                <span class="badge-slim bg-primary">{{ $item->jumlah_pinjam }}x</span>
                            </td>
                            <td class="text-center">
                                @php
                                    $max = $data['barangSering']->max('jumlah_pinjam');
                                    $percentage = $max > 0 ? ($item->jumlah_pinjam / $max) * 100 : 0;
                                @endphp
                                {{ number_format($percentage, 0) }}%
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                        Tidak ada data
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Barang Menipis -->
    <div class="col-lg-6 mb-4">
        <div class="card-slim h-100">
            <div class="p-3 border-bottom">
                <h6 class="fw-bold text-warning mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Barang Menipis
                </h6>
            </div>
            <div class="p-3">
                @if($data['barangMenipis']->count())
                <table class="table table-slim">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th class="text-center">Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['barangMenipis'] as $item)
                        <tr>
                            <td>{{ $item->nama_barang }}</td>
                            <td class="text-center text-white">
                                <span class="badge-slim bg-warning ">{{ $item->stok }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-check-circle fa-2x mb-2"></i><br>
                        Semua stok aman
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const exportPdfBtn = document.querySelector('#export-pdf-btn');
    if (exportPdfBtn) {
        exportPdfBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // Ambil nilai filter
            const startDate = document.querySelector('input[name="start_date"]').value;
            const endDate = document.querySelector('input[name="end_date"]').value;

            // Build URL dengan parameter
            let url = '{{ route("admin.laporan.export-pdf") }}';
            const params = new URLSearchParams();

            if (startDate) params.append('start_date', startDate);
            if (endDate) params.append('end_date', endDate);

            window.location.href = url + '?' + params.toString();
        });
    }
});
</script>
@endsection
