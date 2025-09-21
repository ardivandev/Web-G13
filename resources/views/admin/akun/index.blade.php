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
<div class="row">
    <div class="col-md-12">
        <h1 class="h3 mb-4 text-gray-800">Profil Admin</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card p-4">
            <form method="POST" action="{{ route('admin.akun.updatePassword') }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label>Email</label>
                    <input type="text" class="form-control" value="{{ $user->email }}" readonly>
                </div>

                       <div class="mb-3">
    <label>Password Baru</label>
    <div class="input-group">
        <input type="password" name="password_baru" class="form-control" id="password_baru" required>
        <button class="btn btn-outline-secondary toggle-password" type="button">
            <i class="bi bi-eye"></i>
        </button>
    </div>
    @error('password_baru')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<div class="mb-3">
    <label>Konfirmasi Password Baru</label>
    <div class="input-group">
        <input type="password" name="password_baru_confirmation" class="form-control" id="password_baru_confirmation" required>
        <button class="btn btn-outline-secondary toggle-password" type="button">
            <i class="bi bi-eye"></i>
        </button>
    </div>
</div>

                <button type="submit" class="btn btn-primary">Update Password</button>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', function () {
        const input = this.previousElementSibling;
        const icon = this.querySelector('i');

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        }
    });
});
</script>
@endpush
@endsection
