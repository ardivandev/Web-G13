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
</style>

<h1 class="h3 mb-4 text-gray-800">Tambah Barang</h1>

<div class="card shadow mb-4 bg-white">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.barang.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="nama_barang">Nama Barang</label>
                <input type="text" name="nama_barang" class="form-control" placeholder="Masukkan nama barang" required>
            </div>

            <div class="form-group">
                <label for="spesifikasi">Spesifikasi</label>
                <textarea name="spesifikasi" class="form-control" rows="4" placeholder="Masukkan spesifikasi barang (opsional)"></textarea>
            </div>

            <div class="form-group">
                <label for="stok">Stok</label>
                <input type="number" name="stok" class="form-control" placeholder="Masukkan jumlah stok" required>
            </div>


            <div class="form-group">
                <label for="id_kategori">Kategori</label>
                <select name="id_kategori" class="form-control" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($kategori as $k)
                        <option value="{{ $k->id_kategori }}">{{ $k->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="gambar">Upload Gambar</label>
                <input type="file" name="gambar" class="form-control-file" accept="image/*">
                <small class="text-muted">Format: JPG, PNG, JPEG. Maks 2MB.</small>
            </div>


            <button type="submit" class="btn btn-success">Tambah</button>
            <a href="{{ route('admin.barang.index') }}" class="btn btn-primary">Kembali</a>
        </form>
    </div>
</div>
@endsection
