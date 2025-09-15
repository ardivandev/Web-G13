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

<h1 class="h3 mb-4 text-gray-800">Edit Ruangan</h1>

<div class="card shadow mb-4 bg-white">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.ruangan.update', $ruangan->id_ruangan) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="nama_ruangan">Nama Ruangan</label>
                <input type="text" name="nama_ruangan" class="form-control" value="{{ $ruangan->nama_ruangan }}" placeholder="Masukkan nama ruangan" required>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('admin.ruangan.index') }}" class="btn btn-primary">Kembali</a>
        </form>
    </div>
</div>
@endsection
