@extends('layouts.pengguna.app')

@push('styles')
    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Print Styles --}}
    <style>
        @media print {
            body * { visibility: hidden; }
            #modalBukti, #modalBukti * { visibility: visible; }
            #modalBukti {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 100% !important;
                height: 100% !important;
                z-index: 9999 !important;
            }
            .modal-dialog {
                max-width: 100% !important;
                margin: 0 !important;
                height: 100vh !important;
            }
            .modal-content {
                height: 100% !important;
                border: none !important;
                border-radius: 0 !important;
            }
            .modal-header, .btn-close { display: none !important; }
            .modal-body {
                padding: 20px !important;
                font-size: 12px !important;
                line-height: 1.3 !important;
            }
            .modal-body p { margin-bottom: 8px !important; }
            .modal-body h5, .modal-body h6 {
                margin-bottom: 10px !important;
                margin-top: 15px !important;
                font-size: 14px !important;
            }
            .badge { font-size: 10px !important; padding: 2px 6px !important; }
            .list-group-item { padding: 8px 12px !important; font-size: 11px !important; }
            .mt-3.text-center { display: none !important; }
            .alert { padding: 8px 12px !important; margin-bottom: 10px !important; font-size: 11px !important; }
            .row .col-md-6 { margin-bottom: 5px !important; }
            .list-group, .bg-light { page-break-inside: avoid !important; }
        }
    </style>
@endpush

@section('content')
<audio id="notifAudio" src="{{ asset('sounds/notifikasi.mp3') }}" preload="auto"></audio>
<meta name="csrf-token" content="{{ csrf_token() }}">

<a href="{{ route('pengguna.index') }}" class="btn btn-custom mb-3">Kembali</a>

{{-- Notifikasi --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Terjadi kesalahan:</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Tombol Pilih Role --}}
<div class="mb-3">
    <label class="form-label fw-bold">Role</label>
    <div class="d-flex gap-3">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="role" id="role-siswa" value="siswa" checked>
            <label class="form-check-label" for="role-siswa">Siswa</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="role" id="role-guru" value="guru">
            <label class="form-check-label" for="role-guru">Guru</label>
        </div>
    </div>
</div>

<form action="{{ route('pengguna.peminjaman.store') }}" method="POST">
    @csrf
    {{-- Input Hidden untuk Role --}}
    <input type="hidden" name="role" id="role" value="siswa">

    <div class="card p-4 shadow-sm rounded">

        {{-- Filter Kelas (khusus siswa) --}}
        <div class="mb-3 role-siswa role-field">
            <label for="kelas" class="form-label">Kelas</label>
            <select class="form-select select2" name="kelas" id="kelas">
                <option value="">-- Pilih Kelas --</option>
                @foreach (['X KA','XI KA','XII KA','XIII KA'] as $tingkat)
                    <optgroup label="{{ $tingkat }}">
                        @for ($i=1;$i<=6;$i++)
                            <option value="{{ $tingkat }} {{ $i }}">{{ $tingkat }} {{ $i }}</option>
                        @endfor
                    </optgroup>
                @endforeach
                <optgroup label="X RPL">
                    <option value="X RPL 1">X RPL 1</option>
                    <option value="X RPL 2">X RPL 2</option>
                </optgroup>
                <optgroup label="XI RPL">
                    <option value="XI RPL 1">XI RPL 1</option>
                    <option value="XI RPL 2">XI RPL 2</option>
                </optgroup>
                <optgroup label="XII RPL">
                    <option value="XII RPL 1">XII RPL 1</option>
                    <option value="XII RPL 2">XII RPL 2</option>
                </optgroup>
                <optgroup label="X TKJ">
                    <option value="X TKJ 1">X TKJ 1</option>
                    <option value="X TKJ 2">X TKJ 2</option>
                    <option value="X TKJ 3">X TKJ 3</option>
                </optgroup>
                <optgroup label="XI TKJ">
                    <option value="XI TKJ 1">XI TKJ 1</option>
                    <option value="XI TKJ 2">XI TKJ 2</option>
                    <option value="XI TKJ 3">XI TKJ 3</option>
                </optgroup>
                <optgroup label="XII TKJ">
                    <option value="XII TKJ 1">XII TKJ 1</option>
                    <option value="XII TKJ 2">XII TKJ 2</option>
                    <option value="XII TKJ 3">XII TKJ 3</option>
                </optgroup>
            </select>
        </div>

        {{-- Nama Siswa --}}
        <div class="mb-3 role-siswa role-field">
            <label class="form-label">Nama Siswa</label>
            <select name="id_siswa" id="id_siswa" class="form-select select2">
                <option value="">-- Pilih Siswa --</option>
                @foreach ($siswa as $s)
                    <option value="{{ $s->id_siswa }}" data-kelas="{{ $s->kelas }}">
                        {{ $s->nama_siswa }} ({{ $s->kelas }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Nama Guru --}}
        <div class="mb-3 role-field">
            <label class="form-label">Nama Guru</label>
            <select name="id_guru" id="id_guru" class="form-select select2">
                <option value="">-- Pilih Guru --</option>
                @foreach ($guru as $g)
                    <option value="{{ $g->id_guru }}">{{ $g->nama_guru }}</option>
                @endforeach
            </select>
        </div>

        {{-- Mata Pelajaran --}}
        <div class="mb-3">
            <label class="form-label">Mata Pelajaran</label>
            <select name="id_mapel" class="form-select select2" required>
                <option value="">-- Pilih Mapel --</option>
                @foreach ($mapel as $m)
                    <option value="{{ $m->id_mapel }}">{{ $m->nama_mapel }}</option>
                @endforeach
            </select>
        </div>

        {{-- Ruangan --}}
        <div class="mb-3">
            <label class="form-label">Ruangan</label>
            <select name="id_ruangan" class="form-select select2" required>
                <option value="">-- Pilih Ruangan --</option>
                @foreach ($ruangan as $r)
                    <option value="{{ $r->id_ruangan }}">{{ $r->nama_ruangan }}</option>
                @endforeach
            </select>
        </div>

        {{-- Nomor HP --}}
        <div class="mb-3">
            <label class="form-label">Nomor HP</label>
            <input type="text" name="no_telp" class="form-control" required>
        </div>

        {{-- Waktu KBM --}}
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">Mulai KBM</label>
                <input type="time" name="mulai_kbm" class="form-control" required>
            </div>
            <div class="col">
                <label class="form-label">Selesai KBM</label>
                <input type="time" name="selesai_kbm" class="form-control" required>
            </div>
        </div>

        {{-- Jaminan --}}
        <div class="mb-3 role-siswa role-field">
            <label class="form-label">Jaminan</label>
            <input type="text" name="jaminan" class="form-control">
        </div>

        {{-- Daftar Barang di Keranjang --}}
        @php
            $cart = session('cart', []);
        @endphp
        @if(count($cart) > 0)
            <div class="mb-3">
                <h5 class="fw-bold">Barang yang Akan Dipinjam</h5>
               <ul class="list-group mb-3">
                @foreach ($cart as $key => $item)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ $item['nama'] }} (x{{ $item['jumlah'] }})</span>
                        <div>
                            <span class="badge bg-secondary me-2">Stok: {{ $item['stok'] }}</span>
                            <button type="button" class="btn btn-danger btn-sm remove-item" data-key="{{ $key }}">Hapus</button>
                        </div>
                    </li>
                @endforeach
            </ul>
            </div>
        @else
            <div class="alert alert-warning">Belum ada barang yang dipilih. Silakan pilih barang di halaman utama.</div>
        @endif

        <button type="submit" class="btn btn-primary w-100">Ajukan Peminjaman</button>
    </div>
</form>

{{-- Modal Bukti Peminjaman --}}
<div class="modal fade" id="modalBukti" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-zoom">
        <div class="modal-content shadow-lg rounded-4 border-0">
            <div class="modal-header bg-gradient py-3 px-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-check-circle-fill me-2"></i> Bukti Peminjaman
                </h5>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                @if(session('peminjaman'))
                @php $p = session('peminjaman'); @endphp

                <div class="mb-3">
                    <span class="badge bg-primary fs-6 px-3 py-2">
                        Role: {{ ucfirst($p->role) }}
                    </span>
                </div>

                @if($p->role == 'siswa')
                    <p><strong>Nama Siswa:</strong> {{ $p->siswa->nama_siswa ?? 'N/A' }}
                        @if(isset($p->siswa->kelas))
                            <span class="text-muted">({{ $p->siswa->kelas }})</span>
                        @endif
                    </p>
                @else
                    <p><strong>Nama Guru:</strong> {{ $p->guru->nama_guru ?? 'N/A' }}</p>
                @endif

                <p><strong>Mata Pelajaran:</strong> {{ $p->mapel->nama_mapel ?? 'N/A' }}</p>
                <p><strong>Ruangan:</strong> {{ $p->ruangan->nama_ruangan ?? 'N/A' }}</p>
                <p><strong>No. HP:</strong> {{ $p->no_telp ?? 'N/A' }}</p>
                <p><strong>Waktu:</strong>
                    <span class="badge bg-success">{{ $p->mulai_kbm ?? 'N/A' }}</span> -
                    <span class="badge bg-danger">{{ $p->selesai_kbm ?? 'N/A' }}</span>
                </p>
                <p><strong>Jaminan:</strong> {{ $p->jaminan ?? '-' }}</p>

                <h6 class="mt-4 fw-bold text-secondary">Barang Dipinjam:</h6>

                {{-- FIXED: Improved detail display --}}
                @if(isset($p->detail) && count($p->detail) > 0)
                    <ul class="list-group list-group-flush border rounded-3 mt-2">
                        @foreach($p->detail as $d)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    {{ $d->barang->nama_barang ?? 'Barang tidak ditemukan' }}
                                </span>
                                <span class="badge bg-dark rounded-pill">
                                    x{{ $d->jumlah ?? 1 }}
                                </span>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Total Barang --}}
                    <div class="mt-3 p-3 bg-light rounded">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Total Item: </strong>
                                <span class="badge bg-info">{{ count($p->detail) }} jenis barang</span>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <strong>Total Qty: </strong>
                                <span class="badge bg-success">
                                    {{ $p->detail->sum('jumlah') ?? 0 }} unit
                                </span>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Fallback jika detail tidak ada --}}
                    @php $cart = session('cart', []); @endphp
                    @if(count($cart) > 0)
                        <ul class="list-group list-group-flush border rounded-3 mt-2">
                            @foreach($cart as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $item['nama'] ?? 'N/A' }}</span>
                                    <span class="badge bg-dark rounded-pill">
                                        x{{ $item['jumlah'] ?? 0 }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-3 p-3 bg-light rounded">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Total Item: </strong>
                                    <span class="badge bg-info">{{ count($cart) }} jenis barang</span>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <strong>Total Qty: </strong>
                                    <span class="badge bg-success">
                                        {{ array_sum(array_column($cart, 'jumlah')) }} unit
                                    </span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Tidak ada detail barang yang ditemukan.
                        </div>
                    @endif
                @endif

                {{-- Informasi Tambahan --}}
                <div class="mt-4 p-3 border-start border-primary border-4 bg-light">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Catatan:</strong> Harap simpan bukti peminjaman ini dan tunjukkan kepada petugas lab saat mengambil barang.
                    </small>
                </div>

                {{-- Tombol Download/Close --}}
                <div class="mt-3 text-center">
                    @if(isset($p->id_pinjam))
                        <a href="{{ route('pengguna.peminjaman.download', $p->id_pinjam) }}"
                           class="btn btn-outline-success me-2">
                            <i class="bi bi-download me-1"></i> Download Bukti
                        </a>
                    @else
                        <a href="{{ route('pengguna.peminjaman.download.session') }}"
                           class="btn btn-outline-success me-2">
                            <i class="bi bi-download me-1"></i> Download Bukti
                        </a>
                    @endif
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        <i class="bi bi-check-lg me-1"></i> Selesai
                    </button>
                </div>

                @else
                    <div class="alert alert-danger">
                        <i class="bi bi-x-circle me-2"></i>
                        Data peminjaman tidak ditemukan.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Custom Style + Animasi --}}
<style>
    .modal-header.bg-gradient {
        background: linear-gradient(135deg, #28a745, #218838);
    }

    .modal.fade .modal-dialog.modal-zoom {
        transform: scale(0.8);
        transition: all 0.3s ease-in-out;
    }
    .modal.show .modal-dialog.modal-zoom {
        transform: scale(1);
    }

    .list-group-item {
        transition: all 0.2s ease-in-out;
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }
</style>

{{-- Script untuk Modal --}}
@if(session('peminjaman'))
<script>
    document.addEventListener("DOMContentLoaded", function(){
        var modal = new bootstrap.Modal(document.getElementById('modalBukti'));
        modal.show();
    });
</script>
@endif

@endsection

@push('scripts')
{{-- Select2 JavaScript --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
{{-- Pusher & Echo --}}
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.0/dist/echo.iife.js"></script>

<script>
$(document).ready(function () {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    // Role toggle functionality
    $('input[name="role"]').on('change', function() {
        let role = $(this).val();
        if (role === 'siswa') {
            $('.role-siswa').show();
            $('.role-guru').hide();
        } else {
            $('.role-guru').show();
            $('.role-siswa').hide();
        }
    });

    function toggleRole(role) {
        if (role === 'siswa') {
            $('.role-siswa').show();
            $('.role-guru').hide();
            $('#role').val('siswa');
        } else {
            $('.role-guru').show();
            $('.role-siswa').hide();
            $('#role').val('guru');
        }
    }

    // Default tampil siswa
    toggleRole('siswa');

    $('#role-siswa').on('click', function () {
        toggleRole('siswa');
    });

    $('#role-guru').on('click', function () {
        toggleRole('guru');
    });

    // Filter siswa berdasarkan kelas
    var $kelas = $('#kelas');
    var $siswa = $('#id_siswa');
    var allOptions = $siswa.find('option').clone();

    $kelas.on('change', function () {
        var selectedKelas = $(this).val();
        $siswa.empty().append('<option value="">-- Pilih Siswa --</option>');

        if (selectedKelas) {
            allOptions.each(function () {
                var kelasOption = $(this).data('kelas');
                if (kelasOption && kelasOption === selectedKelas) {
                    $siswa.append($(this).clone());
                }
            });
        } else {
            allOptions.each(function () {
                if ($(this).val() !== '') {
                    $siswa.append($(this).clone());
                }
            });
        }
        $siswa.trigger('change.select2');
    });

    // Handle remove item dari keranjang
    $(document).on('click', '.remove-item', function () {
        let key = $(this).data('key');

        if (confirm('Yakin ingin menghapus barang ini dari keranjang?')) {
            $.ajax({
                url: '{{ route("pengguna.peminjaman.remove", ":key") }}'.replace(':key', key),
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Terjadi kesalahan saat menghapus barang.');
                    }
                },
                error: function (xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        alert(xhr.responseJSON.message);
                    } else {
                        alert('Terjadi kesalahan saat menghapus barang.');
                    }
                }
            });
        }
    });

    // Initialize real-time notifications (Optional for pengguna)
    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '{{ env("PUSHER_APP_KEY") }}',
        cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
        forceTLS: true
    });

    // Listen untuk konfirmasi peminjaman (optional)
   window.Echo.channel('gudang13').listen('.peminjaman.status.update', (e) => {
    if (e.peminjaman) {
        // Tentukan pesan berdasarkan status
        let statusMessage = '';
        const status = e.peminjaman.status.toLowerCase();

         const audio = document.getElementById('notifAudio');
            if (audio) {
                audio.play().catch(err => console.log('Audio blocked'));
            }

        switch(status) {
            case 'dipinjam':
                statusMessage = 'Peminjaman anda telah dikonfirmasi oleh petugas';
                break;
            case 'ditolak':
                statusMessage = 'Peminjaman anda telah ditolak oleh petugas';
                break;
            default:
                statusMessage = `Peminjaman anda telah ${status}`;
                break;
        }

        // Show alert for status update
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-info alert-dismissible fade show position-fixed';
        alertDiv.style.top = '20px';
        alertDiv.style.right = '20px';
        alertDiv.style.zIndex = '9999';
        alertDiv.style.minWidth = '300px';
        alertDiv.innerHTML = `
            <i class="fas fa-bell me-2"></i>
            <strong>Update Status!</strong><br>
            ${statusMessage}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);

        // Auto remove alert after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
});
});
</script>
@endpush
