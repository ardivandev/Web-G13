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

<h1 class="h3 mb-4 text-gray-800">Edit Guru</h1>

<div class="card shadow mb-4 bg-white">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.guru.update', $guru->id_guru) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" name="nama_guru" class="form-control"
                       value="{{ $guru->nama_guru }}" placeholder="Masukkan nama lengkap" required>
            </div>

            <div class="form-group">
                <label for="nip">NIP</label>
                <input type="text" name="nip" class="form-control"
                       value="{{ $guru->nip }}" placeholder="Masukkan NIP" required>
            </div>

            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
            <a href="{{ route('admin.guru.index') }}" class="btn btn-primary">Kembali</a>
        </form>
    </div>
</div>
@endsection
