<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{asset('images/logo.png')}}" type="image/x-icon">
    {{-- <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <title>G13</title>

    <style>
        body {
            background-image: url("images/gambar.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            height: 100vh;
        }
        .login-box {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            width: 95%;
            max-width: 400px;
            animation: fadeIn 0.8s ease-in-out;
        }
        .btn-green {
            background-color: #565477;
            color: white;
        }
        .btn-green:hover {
            background-color: #39384C;
            color: white;
        }
        .title {
            color: #565477;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .input-group-text {
            background-color: #f8f9fa;
        }
        .toggle-eye {
            cursor: pointer;
        }
    </style>
</head>
<body>
    {{-- Notifikasi sukses --}}
    @if (session('success'))
        <div class="d-flex justify-content-center mt-4">
            <div class="alert alert-success text-center w-50" role="alert">
                {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- Notifikasi error --}}
    @if ($errors->any())
        <div class="d-flex justify-content-center mt-4">
            <div class="alert alert-danger text-center w-50" role="alert">
                {{ $errors->first() }}
            </div>
        </div>
    @endif

    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="login-box">
            <h2 class="text-center mb-3 title">Login</h2>
            <form method="POST" action="/login">
                @csrf

                <!-- Email -->
                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                </div>

                <!-- Password -->
                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" class="form-control" name="password" id="pwd" placeholder="Password" required>
                    <span class="input-group-text toggle-eye" id="togglePassword">
                        <i class="bi bi-eye-fill"></i>
                    </span>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-green">Login</button>
                </div>

                <hr>
            </form>
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    {{-- Script --}}
    <script>
        // Menghilangkan alert setelah 3 detik
        setTimeout(function () {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = 0;
                setTimeout(() => alert.remove(), 500);
            });
        }, 3000);

        // Toggle mata password
        const pwdInput = document.getElementById('pwd');
        const toggle = document.getElementById('togglePassword');

        toggle.addEventListener('click', function () {
            const icon = this.querySelector('i');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                icon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
            } else {
                pwdInput.type = 'password';
                icon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
            }
        });
    </script>
</body>
</html>
