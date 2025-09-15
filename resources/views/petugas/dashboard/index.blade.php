@extends('layouts.petugas.app')

@section('content')
<h2 class="mb-4 font-weight-bold">Dashboard Petugas</h2>
<hr class="my-4">

<div class="row">
    <!-- Grafik Batang -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">Grafik Batang Peminjaman & Pengembalian</div>
            <div class="card-body">
                <canvas id="chartPeminjaman" class="chart-small"></canvas>
            </div>
        </div>
    </div>

    <!-- Grafik Garis -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header bg-success text-white">Grafik Garis Peminjaman & Pengembalian</div>
            <div class="card-body">
                <canvas id="lineChart" class="chart-small"></canvas>
            </div>
        </div>
    </div>

    <!-- Grafik Stok Barang -->
    <div class="col-lg-12 mb-4">
        <div class="card shadow">
            <div class="card-header bg-info text-white">Stok Barang</div>
            <div class="card-body">
                <canvas id="stokChart" class="chart-small"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">

    <!-- Total Siswa -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Siswa</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahSiswa }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-person fs-3 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Guru -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Guru</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahGuru }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-person-gear fs-3 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Petugas -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Petugas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahPetugas }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people fs-3 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Barang -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Barang</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahBarang }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-box fs-3 text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Mapel -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Mapel</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahMapel }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-journal-text fs-3 text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Kategori -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-secondary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Total Kategori</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahKategori }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-tags fs-3 text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Ruangan -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-dark shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Total Ruangan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahRuangan }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-door-open fs-3 text-dark"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Peminjaman -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Peminjaman</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahPeminjaman }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-arrow-left-right fs-3 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Pengembalian -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pengembalian</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahPengembalian }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-arrow-counterclockwise fs-3 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    const peminjamanData = {!! json_encode(array_values($peminjamanBulananLengkap)) !!};
    const pengembalianData = {!! json_encode(array_values($pengembalianBulananLengkap)) !!};

    const stokLabels = {!! json_encode($stokBarang->keys()->values()) !!};
    const stokValues = {!! json_encode($stokBarang->values()) !!};

    // Grafik Batang
    new Chart(document.getElementById('chartPeminjaman').getContext('2d'), {
        type: 'bar',
        data: {
            labels: bulanLabels,
            datasets: [
                { label: 'Peminjaman', data: peminjamanData, backgroundColor: 'rgba(54, 162, 235, 0.6)' },
                { label: 'Pengembalian', data: pengembalianData, backgroundColor: 'rgba(75, 192, 192, 0.6)' }
            ]
        }
    });

    // Grafik Garis
    new Chart(document.getElementById('lineChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: bulanLabels,
            datasets: [
                { label: 'Peminjaman', data: peminjamanData, borderColor: 'rgba(54, 162, 235, 1)', fill: false, tension: 0.1 },
                { label: 'Pengembalian', data: pengembalianData, borderColor: 'rgba(75, 192, 192, 1)', fill: false, tension: 0.1 }
            ]
        }
    });

    // Grafik Stok Barang
    new Chart(document.getElementById('stokChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: stokLabels,
            datasets: [{ label: 'Stok Barang', data: stokValues, backgroundColor: 'rgba(255, 206, 86, 0.6)' }]
        }
    });
</script>
@endsection
