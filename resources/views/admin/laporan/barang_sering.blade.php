@extends('layouts.admin.app')

@section('content')
<div class="container-fluid py-4">
    {{-- Header Section --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <a href="{{ route(auth()->guard('admin')->check() ? 'admin.laporan.index' : 'petugas.laporan.index', request()->all()) }}"
                       class="btn btn-outline-light me-3">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="page-title mb-2">
                            <i class="fas fa-trophy me-3"></i>
                            Detail Barang Populer
                        </h1>
                        <p class="page-subtitle mb-0">Analisis lengkap barang yang sering dipinjam</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-flex gap-2 justify-content-end">
                    <button class="btn btn-outline-light" onclick="printReport()">
                        <i class="fas fa-print me-2"></i>Cetak
                    </button>
                    <button class="btn btn-light" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt me-2"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Period Info --}}
    <div class="filter-section mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h6 class="mb-0 text-secondary">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
                </h6>
                <small class="text-muted">Total {{ $barangSering->count() }} barang ditemukan</small>
            </div>
            <div class="col-md-4 text-end">
                <div class="text-muted">
                    <i class="fas fa-clock me-1"></i>
                    Diperbarui: {{ now()->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    @if($barangSering->count() > 0)
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="custom-card stats-card h-100">
                <div class="stats-icon stats-warning">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="stats-value text-warning">{{ $barangSering->first()->nama_barang }}</div>
                <div class="stats-label">Barang Terpopuler</div>
                <div class="mt-2">
                    <small class="text-warning">
                        <i class="fas fa-fire me-1"></i>
                        {{ $barangSering->first()->total_peminjaman }} kali dipinjam
                    </small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="custom-card stats-card h-100">
                <div class="stats-icon stats-primary">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stats-value text-primary">{{ number_format($barangSering->sum('total_peminjaman')) }}</div>
                <div class="stats-label">Total Semua Peminjaman</div>
                <div class="mt-2">
                    <small class="text-primary">
                        <i class="fas fa-trending-up me-1"></i>
                        Dari {{ $barangSering->count() }} jenis barang
                    </small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="custom-card stats-card h-100">
                <div class="stats-icon stats-info">
                    <i class="fas fa-cubes"></i>
                </div>
                <div class="stats-value text-info">{{ number_format($barangSering->sum('total_jumlah_dipinjam')) }}</div>
                <div class="stats-label">Total Unit Dipinjam</div>
                <div class="mt-2">
                    <small class="text-info">
                        <i class="fas fa-boxes me-1"></i>
                        Rata-rata {{ number_format($barangSering->avg('rata_rata_per_peminjaman'), 1) }} per peminjaman
                    </small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="custom-card stats-card h-100">
                <div class="stats-icon {{ $barangSering->where('stok', '<=', 5)->count() > 0 ? 'stats-danger' : 'stats-success' }}">
                    <i class="fas fa-{{ $barangSering->where('stok', '<=', 5)->count() > 0 ? 'exclamation-triangle' : 'shield-alt' }}"></i>
                </div>
                <div class="stats-value {{ $barangSering->where('stok', '<=', 5)->count() > 0 ? 'text-danger' : 'text-success' }}">
                    {{ $barangSering->where('stok', '<=', 5)->count() }}
                </div>
                <div class="stats-label">Stok Menipis</div>
                <div class="mt-2">
                    <small class="{{ $barangSering->where('stok', '<=', 5)->count() > 0 ? 'text-danger' : 'text-success' }}">
                        <i class="fas fa-{{ $barangSering->where('stok', '<=', 5)->count() > 0 ? 'exclamation-circle' : 'check-circle' }} me-1"></i>
                        {{ $barangSering->where('stok', '<=', 5)->count() > 0 ? 'Perlu Perhatian' : 'Kondisi Aman' }}
                    </small>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Top 3 Highlight --}}
    @if($barangSering->count() >= 3)
    <div class="row g-4 mb-4">
        @foreach($barangSering->take(3) as $index => $item)
        <div class="col-lg-4">
            <div class="custom-card h-100 {{ $index == 0 ? 'border-warning' : ($index == 1 ? 'border-secondary' : 'border-info') }}"
                 style="border-width: 3px !important;">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        @if($index == 0)
                            <div class="display-4 text-warning"><i class="fas fa-crown"></i></div>
                            <h5 class="text-warning fw-bold">🥇 JUARA 1</h5>
                        @elseif($index == 1)
                            <div class="display-4 text-secondary"><i class="fas fa-medal"></i></div>
                            <h5 class="text-secondary fw-bold">🥈 JUARA 2</h5>
                        @else
                            <div class="display-4 text-info"><i class="fas fa-award"></i></div>
                            <h5 class="text-info fw-bold">🥉 JUARA 3</h5>
                        @endif
                    </div>
                    <h4 class="fw-bold mb-2">{{ $item->nama_barang }}</h4>
                    <p class="text-muted mb-3">{{ $item->nama_kategori }}</p>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stats-mini">
                                <div class="fw-bold {{ $index == 0 ? 'text-warning' : ($index == 1 ? 'text-secondary' : 'text-info') }}">
                                    {{ $item->total_peminjaman }}
                                </div>
                                <small class="text-muted">Peminjaman</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-mini">
                                <div class="fw-bold {{ $index == 0 ? 'text-warning' : ($index == 1 ? 'text-secondary' : 'text-info') }}">
                                    {{ $item->total_jumlah_dipinjam }}
                                </div>
                                <small class="text-muted">Unit</small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge {{ $item->stok <= 5 ? 'bg-danger' : 'bg-success' }} px-3 py-2">
                            <i class="fas fa-warehouse me-1"></i>
                            Stok: {{ $item->stok }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Detailed Table --}}
    <div class="custom-card">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <i class="fas fa-list me-2 text-primary"></i>
                    Daftar Lengkap Barang Populer
                </h5>
                <small class="text-muted">Diurutkan berdasarkan frekuensi peminjaman tertinggi</small>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary btn-sm" onclick="exportTableToCSV()">
                    <i class="fas fa-download me-1"></i>Export CSV
                </button>
                <div class="btn-group" role="group">
                    <button class="btn btn-outline-secondary btn-sm" onclick="toggleView('compact')" id="btn-compact">
                        <i class="fas fa-compress-alt me-1"></i>Ringkas
                    </button>
                    <button class="btn btn-outline-secondary btn-sm active" onclick="toggleView('detailed')" id="btn-detailed">
                        <i class="fas fa-expand-alt me-1"></i>Detail
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($barangSering->count() > 0)
            <div class="table-responsive">
                <table class="table custom-table mb-0" id="itemsTable">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Peringkat</th>
                            <th>Nama Barang</th>
                            <th class="detailed-col">Spesifikasi</th>
                            <th>Kategori</th>
                            <th class="text-center">Stok</th>
                            <th class="text-center">Frekuensi</th>
                            <th class="text-center">Total Unit</th>
                            <th class="text-center">Rata-rata</th>
                            <th class="text-center detailed-col">Terakhir Dipinjam</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($barangSering as $index => $item)
                        <tr>
                            <td class="text-center">
                                @if($index == 0)
                                    <div class="rank-badge rank-1">
                                        <i class="fas fa-crown me-1"></i>
                                        <span class="fw-bold">#1</span>
                                    </div>
                                @elseif($index == 1)
                                    <div class="rank-badge rank-2">
                                        <i class="fas fa-medal me-1"></i>
                                        <span class="fw-bold">#2</span>
                                    </div>
                                @elseif($index == 2)
                                    <div class="rank-badge rank-3">
                                        <i class="fas fa-award me-1"></i>
                                        <span class="fw-bold">#3</span>
                                    </div>
                                @else
                                    <span class="badge bg-light text-dark px-2 py-1">
                                        <strong>#{{ $index + 1 }}</strong>
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->nama_barang }}</div>
                            </td>
                            <td class="detailed-col">
                                <small class="text-muted">{{ $item->spesifikasi ?? 'Tidak ada spesifikasi' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info">
                                    <i class="fas fa-tag me-1"></i>{{ $item->nama_kategori }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $item->stok <= 5 ? 'bg-danger' : ($item->stok <= 10 ? 'bg-warning' : 'bg-success') }} px-2 py-1">
                                    {{ $item->stok }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="fw-bold text-primary fs-5">{{ $item->total_peminjaman }}</div>
                                <small class="text-muted">kali</small>
                            </td>
                            <td class="text-center">
                                <div class="fw-semibold">{{ $item->total_jumlah_dipinjam }}</div>
                                <small class="text-muted">unit</small>
                            </td>
                            <td class="text-center">
                                <div class="fw-semibold text-info">{{ number_format($item->rata_rata_per_peminjaman, 1) }}</div>
                                <small class="text-muted">unit/pinjam</small>
                            </td>
                            <td class="text-center detailed-col">
                                @if(isset($item->terakhir_dipinjam))
                                    <div class="text-dark">{{ \Carbon\Carbon::parse($item->terakhir_dipinjam)->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($item->terakhir_dipinjam)->diffForHumans() }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->stok <= 5)
                                    <span class="badge bg-danger-subtle text-danger px-2 py-1">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Kritis
                                    </span>
                                @elseif($item->stok <= 10)
                                    <span class="badge bg-warning-subtle text-warning px-2 py-1">
                                        <i class="fas fa-exclamation-circle me-1"></i>Rendah
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success px-2 py-1">
                                        <i class="fas fa-check-circle me-1"></i>Aman
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination if needed --}}
            @if($barangSering->count() > 20)
            <div class="card-footer bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">
                            Menampilkan {{ $barangSering->count() }} dari {{ $barangSering->count() }} barang
                        </small>
                    </div>
                    <div class="col-md-6 text-end">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Data diurutkan berdasarkan frekuensi peminjaman
                        </small>
                    </div>
                </div>
            </div>
            @endif
            @else
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-search fa-4x text-muted"></i>
                </div>
                <h4 class="text-muted mb-3">Tidak Ada Data</h4>
                <p class="text-muted mb-4">Tidak ada data peminjaman barang dalam periode yang dipilih.</p>
                <a href="{{ route(auth()->guard('admin')->check() ? 'admin.laporan.index' : 'petugas.laporan.index') }}"
                   class="btn btn-primary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Laporan
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- Analytics Insights --}}
    @if($barangSering->count() > 0)
    <div class="row g-4 mt-4">
        <div class="col-lg-8">
            <div class="custom-card">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2 text-primary"></i>
                        Analisis & Insight
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="insight-card p-3 border rounded">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-fire text-danger me-2"></i>
                                    <h6 class="mb-0 fw-bold">Demand Tertinggi</h6>
                                </div>
                                <p class="mb-1">{{ $barangSering->first()->nama_barang }}</p>
                                <small class="text-muted">
                                    Dipinjam {{ $barangSering->first()->total_peminjaman }} kali
                                    ({{ number_format(($barangSering->first()->total_peminjaman / $barangSering->sum('total_peminjaman')) * 100, 1) }}% dari total)
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="insight-card p-3 border rounded">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-balance-scale text-info me-2"></i>
                                    <h6 class="mb-0 fw-bold">Rata-rata Peminjaman</h6>
                                </div>
                                <p class="mb-1">{{ number_format($barangSering->avg('total_peminjaman'), 1) }} kali per barang</p>
                                <small class="text-muted">
                                    Dengan rata-rata {{ number_format($barangSering->avg('rata_rata_per_peminjaman'), 1) }} unit per transaksi
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="insight-card p-3 border rounded">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                    <h6 class="mb-0 fw-bold">Peringatan Stok</h6>
                                </div>
                                @php $lowStock = $barangSering->where('stok', '<=', 5)->count(); @endphp
                                <p class="mb-1">{{ $lowStock }} barang stok menipis</p>
                                <small class="text-muted">
                                    @if($lowStock > 0)
                                        Segera lakukan restocking untuk menghindari kehabisan stok
                                    @else
                                        Semua barang populer memiliki stok yang mencukupi
                                    @endif
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="insight-card p-3 border rounded">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-trending-up text-success me-2"></i>
                                    <h6 class="mb-0 fw-bold">Optimasi Stok</h6>
                                </div>
                                @php
                                    $topItems = $barangSering->take(5);
                                    $needsRestock = $topItems->where('stok', '<=', 10)->count();
                                @endphp
                                <p class="mb-1">{{ $needsRestock }} dari 5 teratas perlu penambahan</p>
                                <small class="text-muted">
                                    Fokus pada barang dengan demand tinggi untuk ROI terbaik
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="fw-bold text-success mb-2">
                            <i class="fas fa-lightbulb me-2"></i>Rekomendasi Strategis
                        </h6>
                        <ul class="mb-0 list-unstyled">
                            @if($lowStock > 0)
                            <li class="mb-2">
                                <i class="fas fa-arrow-right text-primary me-2"></i>
                                <strong>Prioritas Restocking:</strong> {{ $lowStock }} barang populer dengan stok menipis memerlukan pengadaan segera
                            </li>
                            @endif
                            <li class="mb-2">
                                <i class="fas fa-arrow-right text-primary me-2"></i>
                                <strong>Optimasi Inventori:</strong> Tingkatkan stok 3 barang teratas untuk memenuhi demand tinggi
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-arrow-right text-primary me-2"></i>
                                <strong>Prediksi Kebutuhan:</strong> Monitor tren peminjaman untuk perencanaan stok jangka panjang
                            </li>
                            <li class="mb-0">
                                <i class="fas fa-arrow-right text-primary me-2"></i>
                                <strong>Efisiensi Operasional:</strong> Pertimbangkan penempatan barang populer di lokasi yang mudah diakses
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="custom-card">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2 text-warning"></i>
                        Distribusi Kategori
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $categories = $barangSering->groupBy('nama_kategori');
                        $categoryStats = $categories->map(function($items, $category) {
                            return [
                                'name' => $category,
                                'count' => $items->count(),
                                'total_loans' => $items->sum('total_peminjaman'),
                                'percentage' => 0
                            ];
                        })->sortByDesc('total_loans');

                        $totalLoans = $categoryStats->sum('total_loans');
                        $categoryStats = $categoryStats->map(function($item) use ($totalLoans) {
                            $item['percentage'] = $totalLoans > 0 ? ($item['total_loans'] / $totalLoans) * 100 : 0;
                            return $item;
                        });
                    @endphp

                    @foreach($categoryStats->take(8) as $category)
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $category['name'] }}</div>
                            <small class="text-muted">{{ $category['count'] }} jenis barang</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-primary">{{ $category['total_loans'] }}</div>
                            <small class="text-muted">{{ number_format($category['percentage'], 1) }}%</small>
                        </div>
                    </div>
                    @endforeach

                    @if($categoryStats->count() == 0)
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-chart-pie fa-2x mb-2"></i>
                        <p class="mb-0">Tidak ada data kategori</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
:root {
    --primary-color: #2563eb;
    --secondary-color: #64748b;
    --success-color: #059669;
    --warning-color: #d97706;
    --danger-color: #dc2626;
    --info-color: #0891b2;
    --light-bg: #f8fafc;
    --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    --card-shadow-hover: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

body {
    background-color: var(--light-bg);
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
}

.page-header {
    background: linear-gradient(135deg, var(--warning-color) 0%, #f59e0b 100%);
    color: white;
    padding: 2rem;
    margin: -1.5rem -1.5rem 2rem -1.5rem;
    border-radius: 0 0 1rem 1rem;
    box-shadow: var(--card-shadow);
}

.page-title {
    font-size: 1.875rem;
    font-weight: 700;
    margin: 0;
}

.page-subtitle {
    opacity: 0.9;
    font-size: 1rem;
}

.filter-section {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: var(--card-shadow);
    border: 1px solid #e2e8f0;
}

.custom-card {
    background: white;
    border-radius: 0.75rem;
    box-shadow: var(--card-shadow);
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    overflow: hidden;
}

.custom-card:hover {
    box-shadow: var(--card-shadow-hover);
    transform: translateY(-1px);
}

.card-header-custom {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-bottom: 1px solid #e2e8f0;
    padding: 1.25rem;
    font-weight: 600;
    color: var(--secondary-color);
}

.stats-card {
    text-align: center;
    padding: 2rem 1.5rem;
}

.stats-icon {
    width: 4rem;
    height: 4rem;
    border-radius: 1rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.stats-value {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    line-height: 1.2;
}

.stats-label {
    color: var(--secondary-color);
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.stats-mini {
    text-align: center;
}

.stats-primary { background-color: #dbeafe; color: var(--primary-color); }
.stats-success { background-color: #dcfce7; color: var(--success-color); }
.stats-info { background-color: #cffafe; color: var(--info-color); }
.stats-warning { background-color: #fef3c7; color: var(--warning-color); }
.stats-danger { background-color: #fef2f2; color: var(--danger-color); }

.custom-table th {
    background-color: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
    font-weight: 600;
    color: var(--secondary-color);
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
    padding: 1rem 0.75rem;
}

.custom-table td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}

.custom-table tr:hover {
    background-color: #f8fafc;
}

.rank-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 0.75rem;
    border-radius: 0.75rem;
    font-weight: 700;
    font-size: 0.875rem;
}

.rank-1 {
    background: linear-gradient(135deg, #ffd700, #ffed4e);
    color: #92400e;
    box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
}

.rank-2 {
    background: linear-gradient(135deg, #c0c0c0, #e5e5e5);
    color: #374151;
    box-shadow: 0 4px 12px rgba(192, 192, 192, 0.3);
}

.rank-3 {
    background: linear-gradient(135deg, #cd7f32, #d2691e);
    color: white;
    box-shadow: 0 4px 12px rgba(205, 127, 50, 0.3);
}

.insight-card {
    transition: all 0.3s ease;
    background: white;
}

.insight-card:hover {
    border-color: var(--primary-color) !important;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
    transform: translateY(-2px);
}

.bg-primary-subtle { background-color: #eff6ff !important; }
.bg-success-subtle { background-color: #f0fdf4 !important; }
.bg-info-subtle { background-color: #f0f9ff !important; }
.bg-warning-subtle { background-color: #fffbeb !important; }
.bg-danger-subtle { background-color: #fef2f2 !important; }

.text-primary { color: var(--primary-color) !important; }
.text-success { color: var(--success-color) !important; }
.text-info { color: var(--info-color) !important; }
.text-warning { color: var(--warning-color) !important; }
.text-danger { color: var(--danger-color) !important; }

.detailed-col {
    display: table-cell;
}

.compact-view .detailed-col {
    display: none;
}

@media (max-width: 768px) {
    .page-header {
        padding: 1.5rem;
        margin: -1rem -1rem 1.5rem -1rem;
    }

    .page-title {
        font-size: 1.5rem;
    }

    .stats-card {
        padding: 1.5rem 1rem;
    }

    .stats-value {
        font-size: 1.5rem;
    }

    .detailed-col {
        display: none !important;
    }

    .rank-badge {
        padding: 0.375rem 0.5rem;
        font-size: 0.75rem;
    }
}

@media print {
    .page-header {
        background: #d97706 !important;
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
    }

    .custom-card {
        box-shadow: none !important;
        border: 1px solid #ccc !important;
        break-inside: avoid;
    }

    .btn, .card-footer {
        display: none !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
function toggleView(view) {
    const table = document.getElementById('itemsTable');
    const compactBtn = document.getElementById('btn-compact');
    const detailedBtn = document.getElementById('btn-detailed');

    if (view === 'compact') {
        table.classList.add('compact-view');
        compactBtn.classList.add('active');
        detailedBtn.classList.remove('active');
        localStorage.setItem('tableView', 'compact');
    } else {
        table.classList.remove('compact-view');
        detailedBtn.classList.add('active');
        compactBtn.classList.remove('active');
        localStorage.setItem('tableView', 'detailed');
    }
}

function exportTableToCSV() {
    const table = document.getElementById('itemsTable');
    const rows = table.querySelectorAll('tr');
    let csv = [];

    for (let i = 0; i < rows.length; i++) {
        const row = [];
        const cols = rows[i].querySelectorAll('td, th');

        for (let j = 0; j < cols.length; j++) {
            let cellData = cols[j].innerText.replace(/\n/g, ' ').replace(/,/g, ';');
            row.push('"' + cellData + '"');
        }
        csv.push(row.join(','));
    }

    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');

    if (link.download !== undefined) {
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'barang_populer_' + new Date().toISOString().split('T')[0] + '.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

function printReport() {
    window.print();
}

// Restore view preference
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('tableView') || 'detailed';
    toggleView(savedView);

    // Add smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// Auto refresh every 10 minutes
setTimeout(() => {
    if (confirm('Data akan diperbarui otomatis. Lanjutkan?')) {
        location.reload();
    }
}, 600000);
</script>
@endpush
