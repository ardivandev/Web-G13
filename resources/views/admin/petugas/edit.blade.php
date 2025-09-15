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

<h1 class="h3 mb-4 text-gray-800">Edit Petugas</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.petugas.update', $petugas->id_petugas) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="nama_petugas">Nama Lengkap</label>
                <input type="text" name="nama_petugas" class="form-control"
                       value="{{ $petugas->nama_petugas }}" placeholder="Masukkan nama lengkap" required>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" class="form-control"
                       value="{{ $petugas->username }}" placeholder="Masukkan username" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control"
                       value="{{ $petugas->email }}" placeholder="Masukkan email petugas" required>
            </div>

            <div class="form-group">
                <label for="password">Password Baru <small class="text-muted">(Kosongkan jika tidak ingin ganti)</small></label>
                <div class="input-group">
                    <input type="password" name="password" id="edit_password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                    <div class="input-group-append">
                        <span class="input-group-text" onclick="togglePassword('edit_password', this)" style="cursor: pointer;">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
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
