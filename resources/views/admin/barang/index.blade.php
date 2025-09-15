@extends('layouts.admin.app')

@section('content')
<style>
    .card {
        border-radius: 0.5rem;
        border: none;
    }
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
    input[type="file"].form-control-sm {
        padding: 0.25rem 0.5rem;
        line-height: 1.5;
        font-size: .875rem;
    }
</style>


@if (session('success'))
    <div class="alert alert-success mt-2" id="success-alert">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger mt-2" id="error-alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>⚠️ : {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<h1 class="h3 mb-4 text-gray-800">Data Barang</h1>

<div class="card shadow mb-4 bg-white">
    <div class="card-body">
        <a href="{{ route('admin.barang.create') }}" class="btn btn-primary mb-3">Tambah Barang</a>

        <div class="card shadow mb-4 bg-white px-3">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <form action="{{ route('admin.barang.index') }}" method="GET" class="form-inline">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control form-control-sm mr-2"
                        placeholder="Cari nama/kode barang">
                    <button type="submit" class="btn btn-sm btn-primary">Cari</button>
                </form>
            </div>

            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Spesifikasi</th>
                            <th>Stok</th>
                            <th>Kategori</th>
                            <th>Gambar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($barang as $i => $b)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $b->nama_barang }}</td>
                            <td>{{ $b->spesifikasi }}</td>
                            <td>{{ $b->stok }}</td>
                            <td>{{ $b->kategori->nama_kategori ?? '-' }}</td>
                            <td>
                                @if($b->gambar)
                                   <div class="text-center">
                                    <img src="{{ asset('images/barang/' . $b->gambar) }}"
                                        alt="{{ $b->nama_barang }}"
                                        width="150" height="150"
                                        style="object-fit: cover; border-radius: 8px;">
                                </div>
                                @else
                                    <span class="text-muted">Tidak ada gambar</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.barang.edit', $b->id_barang) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('admin.barang.destroy', $b->id_barang) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('Yakin hapus?')" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- Import Data Barang --}}
                <!-- <div class="my-3">
                    <h6 class="text-success font-weight-bold mb-3">
                        <i class="fas fa-file-import"></i> Import Data Barang
                    </h6>

                    <form action="{{ route('admin.barang.import') }}" method="POST" enctype="multipart/form-data"
                        class="d-flex flex-wrap align-items-center">
                        @csrf
                        <input type="file" name="file" class="form-control form-control-sm mr-2 mb-2"
                            accept=".xls,.xlsx" required style="max-width:250px; height:auto;">
                        <button class="btn btn-success btn-sm mr-2 mb-2">
                            <i class="fas fa-upload"></i> Import
                        </button>
                        <a href="{{ route('admin.barang.template') }}" class="btn btn-outline-success btn-sm mr-2 mb-2">
                            <i class="fas fa-download"></i> Download Template
                        </a>
                        <small class="text-muted mb-2">
                            📌 Gunakan file Excel (<code>.xls</code> / <code>.xlsx</code>)
                            Kolom: <strong>Nama Barang | Spesifikasi | Stok | Kategori</strong>.
                            Baris pertama dianggap <em>header</em>.
                        </small>
                    </form>
                </div> -->
        </div>
    </div>
</div>

<script>
    setTimeout(() => {
        let success = document.getElementById('success-alert');
        let error = document.getElementById('error-alert');

        if (success) {
            success.style.transition = "opacity 0.5s ease";
            success.style.opacity = "0";
            setTimeout(() => success.remove(), 500);
        }

        if (error) {
            error.style.transition = "opacity 0.5s ease";
            error.style.opacity = "0";
            setTimeout(() => error.remove(), 500);
        }
    }, 3000);
</script>
@endsection
