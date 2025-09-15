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
</style>

<h1 class="h3 mb-4 text-gray-800">Tambah Mata Pelajaran</h1>

<div class="card shadow mb-4 bg-white">
    <div class="card-body">
        <form action="{{ route('admin.mapel.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="nama_mapel">Nama Mapel</label>
                <input type="text" name="nama_mapel" class="form-control" placeholder="Masukkan nama mapel" required>
            </div>

            <div class="form-group">
                <label for="kode_mapel">Kode Mapel</label>
                <input type="text" name="kode_mapel" class="form-control" placeholder="Masukkan kode mapel" required>
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ route('admin.mapel.index') }}" class="btn btn-primary">Kembali</a>
        </form>
    </div>
</div>
@endsection
