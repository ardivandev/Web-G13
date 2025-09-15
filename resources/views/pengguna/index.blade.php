{{-- resources/views/pengguna/index.blade.php --}}
@extends('layouts.pengguna.app')

@section('hero')
<section class="hero"></section>
@endsection

@push('styles')
<style>
    :root {
        --primary-color: #565477;
        --primary-hover: #454266;
    }

    .card-barang {
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        height: 100%;
    }

    .card-barang:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .card-img-top {
        border-radius: 0.375rem 0.375rem 0 0;
        transition: transform 0.3s ease;
    }

    .card-barang:hover .card-img-top {
        transform: scale(1.05);
    }

    .status-gudang {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        padding: 15px;
        border: 1px solid #dee2e6;
    }

    .btn-custom {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
        border: none;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
        color: white;
    }

    .btn-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(86, 84, 119, 0.3);
        background: linear-gradient(135deg, var(--primary-hover) 0%, var(--primary-color) 100%);
    }

    .disabled-link {
        opacity: 0.6;
        cursor: not-allowed !important;
    }

    .search-container {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 15px;
        padding: 20px;
        border: 1px solid #dee2e6;
        margin-bottom: 30px;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(86, 84, 119, 0.25);
    }

    .badge-stok {
        font-size: 0.85em;
        padding: 5px 10px;
        border-radius: 20px;
    }

    .stok-rendah {
        background-color: #dc3545;
        color: white;
    }

    .stok-sedang {
        background-color: #ffc107;
        color: #212529;
    }

    .stok-tinggi {
        background-color: #28a745;
        color: white;
    }

    .input-jumlah {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .input-jumlah:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.1rem rgba(86, 84, 119, 0.25);
    }

    .card-body {
        padding: 1.5rem;
    }

    .card-title {
        color: var(--primary-color);
        font-size: 1.1rem;
        margin-bottom: 0.75rem;
        line-height: 1.3;
        font-weight: 600;
    }

    .card-subtitle {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 4rem;
        color: #dee2e6;
        margin-bottom: 1rem;
    }

    /* Header text styling */
    .text-primary {
        color: var(--primary-color) !important;
    }

    /* Form labels */
    .form-label {
        color: var(--primary-color);
    }

    /* Status gudang text */
    .status-gudang .fw-semibold {
        color: var(--primary-color);
    }

    /* Search result text */
    .search-container strong {
        color: var(--primary-color);
    }

    /* Empty state heading */
    .empty-state h4 {
        color: var(--primary-hover);
    }

    /* Button outline variant */
    .btn-outline-primary {
        color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-outline-primary:hover {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    /* Alert customization */
    .alert-success {
        border-left: 4px solid var(--primary-color);
    }

    /* Pagination active link */
    .page-link.active,
    .active > .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .page-link {
        color: var(--primary-color);
    }

    .page-link:hover {
        color: var(--primary-hover);
        background-color: rgba(86, 84, 119, 0.1);
        border-color: var(--primary-color);
    }

    /* Cart count badge when active */
    .btn-outline-warning {
        color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
    }

    .btn-outline-warning:hover {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
        color: white !important;
    }

    .btn-outline-warning:hover span,
    .btn-outline-warning:hover i {
        color: white !important;
    }

    /* Specific styling for cart badge inside button */
    .btn-custom .badge,
    .btn-outline-warning .badge {
        transition: all 0.3s ease;
    }

    .btn-custom:hover .badge {
        background-color: rgba(255, 255, 255, 0.2) !important;
        color: white !important;
    }

    .btn-outline-warning:hover .badge {
        background-color: rgba(255, 255, 255, 0.2) !important;
        color: white !important;
    }

    #peminjaman-link.cart-active {
    border-color: var(--primary-color) !important;
    color: white;
    background-color: transparent !important;
}
#peminjaman-link.cart-active:hover {
    background-color: var(--primary-color) !important;
    color: white !important;
}


    @media (max-width: 768px) {
        .col-md-4 {
            margin-bottom: 1rem;
        }

        .card-img-top {
            height: 200px !important;
        }

        .d-flex.ms-auto {
            margin-top: 15px !important;
            margin-left: 0 !important;
            width: 100%;
        }

        .btn-custom {
            width: 100%;
            justify-content: center;
        }

        .status-gudang {
            text-align: center;
        }

        .search-container {
            padding: 15px;
        }
    }

    @media (max-width: 576px) {
        .card-img-top {
            height: 180px !important;
        }

        .card-body {
            padding: 1rem;
        }

        .display-6 {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-3">
    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="text-center mb-4">
        <h2 class="fw-bold text-primary mb-2">Daftar Barang Tersedia</h2>
        <p class="text-muted">Pilih barang yang ingin Anda pinjam dari inventori yang tersedia</p>
    </div>

    {{-- Status Gudang & Cart --}}
    <div class="status-gudang mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <span class="me-3 fw-semibold">Status Gudang:</span>
                    <span class="badge {{ $statusGudang === 'buka' ? 'bg-success' : 'bg-danger' }} fs-6 px-3 py-2">
                        <i class="bi bi-{{ $statusGudang === 'buka' ? 'unlock-fill' : 'lock-fill' }} me-1"></i>
                        {{ ucfirst($statusGudang) }}
                    </span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                    <a href="{{ $statusGudang === 'buka' ? route('pengguna.peminjaman.create') : '#' }}"
                       class="btn btn-custom d-flex align-items-center {{ $statusGudang === 'tutup' ? 'disabled-link' : '' }}"
                       id="peminjaman-link">
                        <i class="bi bi-basket-fill me-2"></i>
                        <span>Keranjang Peminjaman</span>
                        <span class="badge bg-light text-dark ms-2 fw-bold" id="cart-count">0</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Search Form --}}
    <div class="search-container">
        <form class="row g-3" method="GET" action="{{ route('pengguna.index') }}">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text"
                           class="form-control border-start-0"
                           placeholder="Cari nama barang, kategori, atau deskripsi..."
                           name="q"
                           value="{{ request('q') }}"
                           style="border-left: none !important;">
                </div>
            </div>
            <div class="col-md-2">
                <button class="btn btn-custom w-100" type="submit">
                    <i class="bi bi-search me-1"></i>
                    Cari
                </button>
            </div>
        </form>

        @if(request('q'))
            <div class="mt-3">
                <span class="text-muted">Hasil pencarian untuk: </span>
                <strong>"{{ request('q') }}"</strong>
                <a href="{{ route('pengguna.index') }}" class="btn btn-outline-secondary btn-sm ms-2">
                    <i class="bi bi-x"></i> Hapus Filter
                </a>
            </div>
        @endif
    </div>

    {{-- Barang Grid --}}
    @if($barang->count() > 0)
        <div class="row g-4" id="barang">
            @foreach ($barang as $item)
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="card card-barang h-100">
                        {{-- Gambar Barang --}}
                        <div class="position-relative overflow-hidden">
                            @if($item->gambar)
                                <img src="{{ asset('images/barang/'.$item->gambar) }}"
                                     class="card-img-top"
                                     alt="{{ $item->nama_barang }}"
                                     style="height: 220px; object-fit: cover;">
                            @else
                                <img src="{{ asset('images/barang/default.jpg') }}"
                                     class="card-img-top"
                                     alt="Tidak ada gambar"
                                     style="height: 220px; object-fit: cover;">
                            @endif

                            {{-- Badge Stok --}}
                            <div class="position-absolute top-0 end-0 m-2">
                                @php
                                    $badgeClass = $item->stok > 10 ? 'stok-tinggi' : ($item->stok > 5 ? 'stok-sedang' : 'stok-rendah');
                                @endphp
                                <span class="badge badge-stok {{ $badgeClass }}">
                                    Stok: <span class="stok-count" data-id="{{ $item->id_barang }}">{{ $item->stok }}</span>
                                </span>
                            </div>
                        </div>

                        <div class="card-body d-flex flex-column">
                            {{-- Info Barang --}}
                            <h5 class="card-title fw-bold">{{ $item->nama_barang }}</h5>
                            <h6 class="card-subtitle text-muted mb-3">
                                <i class="bi bi-tag-fill me-1"></i>
                                {{ $item->kategori->nama_kategori ?? 'Tidak Berkategori' }}
                            </h6>

                            @if($item->deskripsi)
                                <p class="card-text text-muted small mb-3">
                                    {{ Str::limit($item->deskripsi, 80) }}
                                </p>
                            @endif

                            {{-- Form Peminjaman --}}
                            <div class="mt-auto">
                                <form class="add-to-cart-form"
                                      data-id="{{ $item->id_barang }}"
                                      data-name="{{ $item->nama_barang }}"
                                      data-stok="{{ $item->stok }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="jumlah_{{ $item->id_barang }}" class="form-label fw-semibold">
                                            <i class="bi bi-123 me-1"></i>
                                            Jumlah Pinjam
                                        </label>
                                        <input type="number"
                                               name="jumlah"
                                               id="jumlah_{{ $item->id_barang }}"
                                               class="form-control input-jumlah"
                                               min="1"
                                               max="{{ $item->stok }}"
                                               placeholder="Masukkan jumlah"
                                               {{ $item->stok == 0 ? 'disabled' : '' }}
                                               required>
                                    </div>

                                    <input type="hidden" name="barang_id" value="{{ $item->id_barang }}">

                                    <button type="submit"
                                            class="btn btn-custom w-100 {{ $item->stok == 0 ? 'disabled' : '' }}"
                                            {{ $item->stok == 0 ? 'disabled' : '' }}>
                                        <span class="button-text">
                                            <i class="bi bi-plus-circle me-1"></i>
                                            {{ $item->stok == 0 ? 'Stok Habis' : 'Tambah ke Keranjang' }}
                                        </span>
                                        <span class="spinner-border spinner-border-sm d-none" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if(method_exists($barang, 'links'))
            <div class="d-flex justify-content-center mt-5">
                {{ $barang->links() }}
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <h4 class="text-muted">Tidak Ada Barang Ditemukan</h4>
            <p class="text-muted mb-4">
                @if(request('q'))
                    Tidak ditemukan barang dengan kata kunci "{{ request('q') }}"
                @else
                    Belum ada barang yang tersedia saat ini
                @endif
            </p>
            @if(request('q'))
                <a href="{{ route('pengguna.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-1"></i>
                    Kembali ke Semua Barang
                </a>
            @endif
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let statusGudang = "{{ $statusGudang }}";

    // Inisialisasi
    updateCartCount();
    initializeTooltips();

    // Cegah klik ke halaman peminjaman jika gudang tutup
    $('#peminjaman-link').on('click', function(e) {
        if (statusGudang !== 'buka') {
            e.preventDefault();
            showAlert('Gudang sedang tutup, tidak bisa membuka halaman peminjaman!', 'warning');
        }
    });

    // Form submit dengan validasi gudang
    $('.add-to-cart-form').on('submit', function(e) {
        e.preventDefault();

        // Cek status gudang
        if (statusGudang !== 'buka') {
            showAlert('Gudang sedang tutup, tidak bisa melakukan peminjaman!', 'warning');
            return;
        }

        let form = $(this);
        let barangId = form.data('id');
        let barangName = form.data('name');
        let stokBarang = form.data('stok');
        let jumlah = form.find('input[name="jumlah"]').val();
        let submitBtn = form.find('button[type="submit"]');
        let buttonText = submitBtn.find('.button-text');
        let spinner = submitBtn.find('.spinner-border');

        // Validasi input
        if (!jumlah || jumlah <= 0) {
            showAlert('Jumlah harus lebih dari 0', 'warning');
            form.find('input[name="jumlah"]').focus();
            return;
        }

        if (parseInt(jumlah) > parseInt(stokBarang)) {
            showAlert(`Jumlah tidak boleh melebihi stok yang tersedia (${stokBarang})`, 'warning');
            form.find('input[name="jumlah"]').focus().select();
            return;
        }

        // Disable button dan show loading
        submitBtn.prop('disabled', true);
        buttonText.html('<i class="bi bi-hourglass-split me-1"></i>Menambahkan...');
        spinner.removeClass('d-none');

        // AJAX request
        $.ajax({
            url: '{{ route("pengguna.peminjaman.add") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                barang_id: barangId,
                jumlah: jumlah
            },
            success: function(response) {
                if (response.success) {
                    showAlert(`${barangName} berhasil ditambahkan ke keranjang (${jumlah} item)`, 'success');
                    form.find('input[name="jumlah"]').val('');
                    updateCartCount();

                    // Animate success
                    submitBtn.removeClass('btn-custom').addClass('btn-success');
                    buttonText.html('<i class="bi bi-check-circle me-1"></i>Berhasil!');

                    setTimeout(function() {
                        submitBtn.removeClass('btn-success').addClass('btn-custom');
                        buttonText.html('<i class="bi bi-plus-circle me-1"></i>Tambah ke Keranjang');
                    }, 2000);
                } else {
                    showAlert(response.message || 'Terjadi kesalahan saat menambahkan barang', 'danger');
                }
            },
            error: function(xhr) {
                let message = 'Terjadi kesalahan saat menambahkan barang';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    message = Object.values(xhr.responseJSON.errors).join(', ');
                }

                showAlert(message, 'danger');
                console.error('Error:', xhr.responseText);
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                buttonText.html('<i class="bi bi-plus-circle me-1"></i>Tambah ke Keranjang');
                spinner.addClass('d-none');
            }
        });
    });

    // Fungsi untuk menampilkan alert
    function showAlert(message, type) {
        // Remove existing alerts
        $('.alert').remove();

        let alertClass = 'alert-' + type;
        let iconClass = getIconClass(type);

        let alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi ${iconClass} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        $('.container-fluid').prepend(alertHtml);

        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut(500, function() {
                $(this).remove();
            });
        }, 5000);

        // Scroll to top to show alert
        // $('html, body').animate({ scrollTop: 0 }, 300);
    }

    // Fungsi untuk mendapatkan icon berdasarkan type
    function getIconClass(type) {
        const icons = {
            'success': 'bi-check-circle-fill',
            'danger': 'bi-exclamation-triangle-fill',
            'warning': 'bi-exclamation-circle-fill',
            'info': 'bi-info-circle-fill'
        };
        return icons[type] || 'bi-info-circle-fill';
    }

    // Fungsi untuk update cart count
    function updateCartCount() {
        $.ajax({
            url: '{{ route("pengguna.peminjaman.count") }}',
            type: 'GET',
            success: function(response) {
                if (response.count !== undefined) {
                    $('#cart-count').text(response.count);

                    // Animate cart count jika ada perubahan
                    if (response.count > 0) {
    $('#cart-count').removeClass('bg-warning text-dark').addClass('bg-light text-dark');
    $('#peminjaman-link').addClass('cart-active');
} else {
    $('#cart-count').removeClass('bg-warning text-dark').addClass('bg-light text-dark');
    $('#peminjaman-link').removeClass('cart-active');
}
                }
            },
            error: function() {
                console.error('Gagal mengambil jumlah cart');
            }
        });
    }

    // Inisialisasi tooltips
    function initializeTooltips() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Refresh data setiap 30 detik
    setInterval(function() {
        updateCartCount();
    }, 30000);
});
</script>
@endpush
