{{-- resources/views/layouts/pengguna/app.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>G13</title>
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}" type="image/x-icon">

    {{-- Bootstrap CSS --}}
    {{-- <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

     <!-- Pusher Script -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    {{-- Custom Modern Style --}}
    <style>
        :root {
            --primary-color: #565477;
            --primary-hover: #454266;
            --card-bg: #e6e6eb;
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --glass-bg: rgba(255, 255, 255, 0.25);
            --glass-border: rgba(255, 255, 255, 0.18);
            --shadow-soft: 0 8px 32px rgba(31, 38, 135, 0.37);
            --shadow-hover: 0 15px 35px rgba(31, 38, 135, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            position: relative;
        }

        /* Animated particles background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 20% 50%, rgba(86, 84, 119, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(86, 84, 119, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(86, 84, 119, 0.06) 0%, transparent 50%);
            z-index: -1;
            animation: particleFloat 20s ease-in-out infinite alternate;
        }

        @keyframes particleFloat {
            0% { opacity: 0.3; transform: translateY(0px); }
            100% { opacity: 0.6; transform: translateY(-10px); }
        }

        /* Modern Navbar */
        .navbar-custom {
            background: rgba(86, 84, 119, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1030;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(86, 84, 119, 0.15);
        }

        .navbar-custom.scrolled {
            background: rgba(86, 84, 119, 0.98);
            box-shadow: 0 8px 32px rgba(86, 84, 119, 0.2);
        }

        .navbar-brand {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-weight: 700 !important;
            font-size: 1.8rem !important;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: all 0.3s ease;
            text-shadow: 0 2px 10px rgba(255, 255, 255, 0.3);
        }

        .navbar-brand:hover {
            transform: scale(1.05);
            -webkit-text-fill-color: transparent;
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            margin: 0 10px;
            position: relative;
            transition: all 0.3s ease;
            border-radius: 8px;
            padding: 8px 16px !important;
        }

        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .navbar-nav .nav-link:hover::after {
            width: 80%;
        }

        .navbar-nav .nav-link:hover {
            color: white !important;
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        /* Modern Hero Section */
        .hero {
            background: url('{{ asset('images/home.jpg') }}') no-repeat center center;
            background-size: cover;
            height: 500px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white" fill-opacity="0.3"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>');
            animation: heroFloat 15s ease-in-out infinite;
        }

        @keyframes heroFloat {
            0%, 100% { transform: translateX(0px) translateY(0px); }
            50% { transform: translateX(-10px) translateY(-5px); }
        }

        /* Modern Cards */
        .card {
            background: var(--card-bg);
            border: none;
            border-radius: 20px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
            position: relative;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.6s;
            z-index: 1;
        }

        .hover-card:hover::before {
            left: 100%;
        }

        .hover-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: var(--shadow-hover);
            background: rgba(230, 230, 235, 0.9);
        }

        .card-img-top {
            transition: all 0.4s ease;
            border-radius: 20px 20px 0 0;
        }

        .hover-card:hover .card-img-top {
            transform: scale(1.05);
        }

        .card-body {
            position: relative;
            z-index: 2;
            padding: 1.5rem;
        }

        .card-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.8rem;
        }

        .card-subtitle {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Modern Buttons */
        .btn-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(86, 84, 119, 0.3);
        }

        .btn-custom::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s;
        }

        .btn-custom:hover::before {
            left: 100%;
        }

        .btn-custom:hover {
            background: linear-gradient(135deg, var(--primary-hover) 0%, var(--primary-color) 100%);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(86, 84, 119, 0.4);
        }

        .btn-custom:active {
            transform: translateY(0);
        }

        /* Status Badge */
        .badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.85rem;
        }

        .bg-success {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%) !important;
            box-shadow: 0 4px 15px rgba(72, 187, 120, 0.3);
        }

        .bg-danger {
            background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%) !important;
            box-shadow: 0 4px 15px rgba(245, 101, 101, 0.3);
        }

        /* Form Elements */
        .form-control {
            border: 2px solid rgba(86, 84, 119, 0.1);
            border-radius: 12px;
            padding: 12px 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(86, 84, 119, 0.1);
            background: white;
        }

        .input-group .btn-custom {
            border-radius: 0 12px 12px 0;
        }

        .input-group .form-control {
            border-radius: 12px 0 0 12px;
        }

        /* Alert Styling */
        .alert {
            border: none;
            border-radius: 12px;
            padding: 16px 20px;
            backdrop-filter: blur(10px);
            border-left: 4px solid;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(72, 187, 120, 0.1), rgba(56, 161, 105, 0.1));
            border-left-color: #48bb78;
            color: #2f855a;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(245, 101, 101, 0.1), rgba(229, 62, 62, 0.1));
            border-left-color: #f56565;
            color: #c53030;
        }

        .alert-warning {
            background: linear-gradient(135deg, rgba(237, 137, 54, 0.1), rgba(221, 107, 32, 0.1));
            border-left-color: #ed8936;
            color: #c05621;
        }

        /* Modern Footer */
        /* Modern Symmetric Footer - Replace your existing footer CSS with this */
.footer {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    color: white;
    margin-top: 4rem;
    position: relative;
    overflow: hidden;
    padding: 3rem 0 0 0;
}

.footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
}

/* Footer Container */
.footer-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

/* Footer Grid Layout */
.footer-content {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 3rem;
    margin-bottom: 3rem;
}

.footer-section {
    text-align: center;
}

/* Footer Headings */
.footer .box h3,
.footer-section h3 {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 600;
    color: white !important;
    margin-bottom: 1.5rem;
    position: relative;
    font-size: 1.25rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.footer .box h3::after,
.footer-section h3::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 50%;
    transform: translateX(-50%);
    width: 40px;
    height: 2px;
    background: linear-gradient(90deg, rgba(255, 255, 255, 0.8), transparent);
}

/* Footer Links */
.footer-links {
    list-style: none;
    padding: 0;
}

.footer-links li {
    margin-bottom: 0.75rem;
}

.footer .box a,
.footer-links a {
    color: rgba(255, 255, 255, 0.8) !important;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 1rem;
    font-weight: 400;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    display: inline-block;
    position: relative;
}

.footer .box a:hover,
.footer-links a:hover {
    color: white !important;
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
    text-decoration: none;
}

/* Contact Info */
.contact-info {
    list-style: none;
    padding: 0;
}

.contact-info li {
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    color: rgba(255, 255, 255, 0.9);
    font-size: 1rem;
}

.contact-info i {
    width: 20px;
    text-align: center;
    opacity: 0.8;
}

/* Developer List */
.developer-list {
    list-style: none;
    padding: 0;
}

.developer-list li {
    margin-bottom: 0.75rem;
    color: rgba(255, 255, 255, 0.9);
    font-size: 1rem;
    font-weight: 500;
    padding: 0.5rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    border-left: 3px solid rgba(255, 255, 255, 0.3);
}

/* Footer Bottom */
.footer .credit,
.footer-bottom {
    background: rgba(0, 0, 0, 0.2);
    color: rgba(255, 255, 255, 0.8) !important;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    text-align: center;
    padding: 1.5rem 0;
    font-size: 0.95rem;
}

.footer .credit span,
.footer-bottom span {
    color: white !important;
    font-weight: 600;
}

/* Responsive Design */
@media (max-width: 768px) {
    .footer-content {
        grid-template-columns: 1fr;
        gap: 2rem;
        text-align: center;
    }

    .footer-container {
        padding: 0 1rem;
    }

    .contact-info li {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .footer {
        padding: 2rem 0 0 0;
    }

    .footer-content {
        gap: 1.5rem;
    }

    .footer-section h3,
    .footer .box h3 {
        font-size: 1.1rem;
    }

    .footer-links a,
    .contact-info li,
    .developer-list li,
    .footer .box a {
        font-size: 0.9rem;
    }
}

/* Tambahkan CSS ini untuk membuat footer benar-benar simetris */

.footer .box-container {
    max-width: 1300px; /* Batasi lebar container */
    margin: 0 auto; /* Center container */
}

.footer .row {
    justify-content: center; /* Center semua kolom */
    align-items: flex-start;
}

.footer .col-lg-3 {
    flex: 0 0 auto;
    width: 30%; /* Bagi rata 3 kolom: 30% each */
    max-width: 700px; /* Batasi lebar maksimal */
    text-align: center; /* Center align semua konten */
}

/* Styling untuk heading */
.footer .box h3 {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 600;
    color: white !important;
    margin-bottom: 1.5rem;
    position: relative;
    font-size: 1.25rem !important;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-align: center;
}

/* Garis bawah heading di center */
.footer .box h3::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 50%;
    transform: translateX(-50%);
    width: 40px;
    height: 2px;
    background: linear-gradient(90deg, rgba(255, 255, 255, 0.8), transparent);
}

/* Styling links */
.footer .box a {
    color: rgba(255, 255, 255, 0.8) !important;
    transition: all 0.3s ease;
    font-size: 1rem;
    font-weight: 400;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    display: inline-block;
    margin: 0 auto;
    position: relative;
}

.footer .box a:hover {
    color: white !important;
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
    text-decoration: none;
}

/* Khusus untuk developer links styling */
.footer .col-lg-3:last-child .box a {
    background: rgba(255, 255, 255, 0.05);
    border-left: 3px solid rgba(255, 255, 255, 0.3);
    margin-bottom: 0.5rem;
    font-weight: 500;
}

/* Credit section */
.footer .credit {
    background: rgba(0, 0, 0, 0.2);
    color: rgba(255, 255, 255, 0.8) !important;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    margin-top: 3rem;
    padding: 1.5rem 0;
}

.footer .credit span {
    color: white !important;
    font-weight: 600;
}

/* Responsive untuk tablet dan mobile */
@media (max-width: 992px) {
    .footer .col-lg-3 {
        width: 100%;
        margin-bottom: 2rem;
        text-align: center;
    }

    .footer .box-container {
        max-width: 100%;
    }
}

@media (max-width: 768px) {
    .footer .box h3 {
        font-size: 1.1rem !important;
    }

    .footer .box a {
        font-size: 0.9rem;
    }
}

        /* Disabled link styling */
        .disabled-link {
            pointer-events: none;
            opacity: 0.6;
            cursor: not-allowed;
            background: #9ca3af !important;
        }

        /* Loading Animation */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        /* Page Title */
        .display-6 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            color: var(--text-primary);
            position: relative;
            display: inline-block;
        }

        .display-6::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--primary-color), transparent);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero {
                height: 400px;
            }

            .navbar-brand {
                font-size: 1.5rem !important;
            }

            .card-body {
                padding: 1.25rem;
            }

            .btn-custom {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
        }

        /* Scroll reveal animation */
        .animate-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .animate-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, var(--primary-hover), var(--primary-color));
        }

        /* ===============================
   RESPONSIVE DESIGN (UNIVERSAL)
   =============================== */

/* Tablet (≤ 992px) */
@media (max-width: 992px) {
    .navbar-nav {
        text-align: center;
        margin-top: 1rem;
    }

    .navbar-nav .nav-link {
        display: block;
        margin: 8px 0;
    }

    .hero {
        height: 350px;
        text-align: center;
        padding: 20px;
    }

    .card {
        margin-bottom: 1.5rem;
    }

    .footer .row {
        text-align: center;
    }
}

/* Mobile (≤ 768px) */
@media (max-width: 768px) {
    body {
        font-size: 15px;
    }

    .navbar-brand {
        font-size: 1.3rem !important;
    }

    .btn-custom {
        padding: 10px 18px;
        font-size: 0.85rem;
    }

    .card-body {
        padding: 1rem;
    }

    .display-6 {
        font-size: 1.5rem;
    }

    .footer .box h3 {
        font-size: 1.1rem;
    }
}

/* Small Mobile (≤ 480px) */
@media (max-width: 480px) {
    body {
        font-size: 14px;
    }

    .hero {
        height: 250px;
        padding: 10px;
    }

    .navbar-brand {
        font-size: 1.1rem !important;
    }

    .navbar-nav .nav-link {
        padding: 6px 12px !important;
        font-size: 0.9rem;
    }

    .btn-custom {
        padding: 8px 14px;
        font-size: 0.8rem;
    }

    .card-title {
        font-size: 1rem;
    }

    .card-subtitle {
        font-size: 0.8rem;
    }

    .footer {
        padding: 20px 0 0 0 !important;
    }

    .footer .credit {
        font-size: 0.8rem;
    }
}

    </style>

    @stack('styles')
</head>
<body class="bg-light">

{{-- Navbar --}}
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ url('/pengguna/index') }}">G13</a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="{{ url('/pengguna/index') }}">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/pengguna/index#barang') }}">Barang</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/pengguna/tentang') }}">Tentang</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login Admin/Petugas</a></li>
            </ul>
        </div>
    </div>
</nav>

{{-- Hero (opsional) --}}
@yield('hero')

{{-- Main Content --}}
<div class="container my-4">
    @yield('content')
</div>

{{-- Footer --}}
<section class="footer mt-5" style="padding: 40px 20px;">
    <div class="box-container container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="box">
                    <h3 class="fw-bold mb-3" style="font-size:1.3rem;">Quick Links</h3>
                    <a href="{{ url('/pengguna/index') }}" class="d-block mb-2 text-decoration-none">Beranda</a>
                    <a href="{{ url('/pengguna/tentang') }}" class="d-block mb-2 text-decoration-none">Tentang</a>
                    <a href="{{ url('/pengguna/index#barang') }}" class="d-block mb-2 text-decoration-none">Barang</a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="box">
                    <h3 class="fw-bold mb-3" style="font-size:1.3rem;">Info Kontak</h3>
                    <a href="tel:(022) 7318960" class="d-block mb-2 text-decoration-none">
                        <i class="bi bi-telephone me-2"></i>(022) 7318960
                    </a>
                    <a href="mailto:smkn13bandung@gmail.com" class="d-block mb-2 text-decoration-none">
                        <i class="bi bi-envelope me-2"></i>smkn13bandung@gmail.com
                    </a>
                    <a href="#" class="d-block mb-2 text-decoration-none">
                        <i class="bi bi-geo-alt me-2"></i>Indonesia
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="box">
                    <h3 class="fw-bold mb-3" style="font-size:1.3rem;">Developer</h3>
                    <a href="https://instagram.com/ardivannrr" target="_blank" class="d-block mb-2 text-decoration-none">Ardivan Nur Raihan Rahman</a>
                    <a href="https://instagram.com/ghanifrasbasel" target="_blank" class="d-block mb-2 text-decoration-none">Ghanifra Sobia Basel</a>
                    <a href="https://instagram.com/ayydan___" target="_blank" class="d-block mb-2 text-decoration-none">Zaidan Faaris Abidi</a>
                </div>
            </div>

    <div class="credit text-center mt-4" style="font-size:0.9rem;">
        SMKN 13 Bandung | <span>&copy; 2025</span>
    </div>
</section>

{{-- JavaScript --}}
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

<script>
    // Navbar scroll effect
    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 100) {
            $('.navbar-custom').addClass('scrolled');
        } else {
            $('.navbar-custom').removeClass('scrolled');
        }
    });

    // Animate elements on scroll
    function animateOnScroll() {
        $('.animate-in').each(function() {
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();

            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                $(this).addClass('visible');
            }
        });
    }

    $(window).on('scroll resize', animateOnScroll);
    $(document).ready(animateOnScroll);

    // Add animate-in class to cards
    $(document).ready(function() {
        $('.col-md-4').addClass('animate-in');

        // Stagger animation
        $('.col-md-4').each(function(index) {
            $(this).css('transition-delay', (index * 0.1) + 's');
        });
    });
</script>

@stack('scripts')

</body>
</html>
