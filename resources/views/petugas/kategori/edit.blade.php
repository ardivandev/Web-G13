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
</style>

<h1 class="h3 mb-4 text-gray-800">Edit Kategori</h1>

<div class="card shadow mb-4 bg-white">
    <div class="card-body">
        <form method="POST" action="{{ route('petugas.kategori.update', $kategori->id_kategori) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="nama_kategori">Nama Kategori</label>
                <input type="text" name="nama_kategori" class="form-control" value="{{ $kategori->nama_kategori }}" placeholder="Masukkan nama kategori" required>
            </div>

            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
            <a href="{{ route('petugas.kategori.index') }}" class="btn btn-primary">Kembali</a>
        </form>
    </div>
</div>
@endsection
