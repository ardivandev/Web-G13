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

<h1 class="h3 mb-4 text-gray-800">Tambah Siswa</h1>

<div class="card shadow mb-4 bg-white">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.siswa.store') }}">
            @csrf

            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" name="nama_siswa" class="form-control" placeholder="Masukkan nama lengkap" required>
            </div>

            <div class="form-group">
                <label for="nis">NIS</label>
                <input type="text" name="nis" class="form-control" placeholder="Masukkan NIS" maxlength="11" required>
            </div>

            <div class="form-group">
                <label for="kelas">Kelas</label>
                <input type="text" name="kelas" class="form-control" placeholder="Masukkan kelas" required>
            </div>

            <button type="submit" class="btn btn-success">Tambah</button>
            <a href="{{ route('admin.siswa.index') }}" class="btn btn-primary">Kembali</a>
        </form>
    </div>
</div>
@endsection
