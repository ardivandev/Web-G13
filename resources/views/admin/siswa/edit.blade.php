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

<h1 class="h3 mb-4 text-gray-800">Edit Siswa</h1>

<div class="card shadow mb-4 bg-white">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.siswa.update', $siswa->id_siswa) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="nama_siswa">Nama Lengkap</label>
                <input type="text" name="nama_siswa" class="form-control"
                       value="{{ $siswa->nama_siswa }}" placeholder="Masukkan nama lengkap" required>
            </div>

            <div class="form-group">
                <label for="nis">NIS</label>
                <input type="text" name="nis" class="form-control"
                       value="{{ $siswa->nis }}" placeholder="Masukkan NIS" required>
            </div>

            <div class="form-group">
                <label for="kelas">Kelas</label>
                <input type="text" name="kelas" class="form-control"
                       value="{{ $siswa->kelas }}" placeholder="Masukkan kelas" required>
            </div>

            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
            <a href="{{ route('admin.siswa.index') }}" class="btn btn-primary">Kembali</a>
        </form>
    </div>
</div>
@endsection
