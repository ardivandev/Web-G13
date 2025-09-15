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

<h1 class="h3 mb-4 text-gray-800">Tambah Petugas</h1>

<div class="card shadow mb-4 bg-white">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.petugas.store') }}">
            @csrf

            <div class="form-group">
                <label for="nama_petugas">Nama Lengkap</label>
                <input type="text" name="nama_petugas" class="form-control" placeholder="Masukkan nama lengkap" required>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Masukkan email petugas" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
                    <div class="input-group-append">
                        <span class="input-group-text" onclick="togglePassword('password', this)" style="cursor: pointer;">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                </div>
            </div>


            <button type="submit" class="btn btn-success">Tambah</button>
            <a href="{{ route('admin.petugas.index') }}" class="btn btn-primary">Kembali</a>
        </form>
    </div>
</div>
<script>
function togglePassword(id, el) {
    const input = document.getElementById(id);
    const icon = el.querySelector("i");

    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
    }
}
</script>

@endsection
