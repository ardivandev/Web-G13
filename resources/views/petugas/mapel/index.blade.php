@extends('layouts.petugas.app')

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

<h1 class="h3 mb-4 text-gray-800">Data Mata Pelajaran</h1>

<div class="card shadow mb-4 bg-white">
    <div class="card-body">
        <div class="card shadow mb-4 bg-white px-3">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <form action="{{ route('petugas.mapel.index') }}" method="GET" class="form-inline">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm mr-2" placeholder="Cari mapel">
                    <button type="submit" class="btn btn-sm btn-primary">Cari</button>
                </form>
            </div>

            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Mapel</th>
                            <th>Kode Mapel</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mapel as $i => $m)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $m->nama_mapel }}</td>
                            <td>{{ $m->kode_mapel }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
              </div>
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
