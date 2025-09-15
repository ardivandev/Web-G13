{{-- resources/views/petugas/peminjaman/create.blade.php --}}
@extends('layouts.petugas.app')

@push('styles')
    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .role-field {
            transition: all 0.3s ease;
        }
        .barang-group {
            border: 1px solid #e3e6f0;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #f8f9fc;
        }
    </style>
@endpush

@section('content')


@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-2" id="success-alert">
        <i class="fas fa-check-circle"></i>
        <strong>Berhasil!</strong> {{ session('success') }}
        {{-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> --}}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-2" id="error-alert">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Peringatan!</strong>
        <div style="white-space: pre-line; margin-top: 8px;">{{ session('error') }}</div>
        {{-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> --}}
    </div>
@endif


<h1 class="h3 mb-4 text-gray-800">Tambah Peminjaman</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <form method="POST" action="{{ route('petugas.peminjaman.store') }}">
            @csrf

            {{-- Pilih Role --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Role</label>
                <div class="d-flex gap-3">
                    <div class="form-check mr-3">
                        <input class="form-check-input" type="radio" name="role" id="role-siswa" value="siswa" checked>
                        <label class="form-check-label" for="role-siswa">Siswa</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="role" id="role-guru" value="guru">
                        <label class="form-check-label" for="role-guru">Guru</label>
                    </div>
                </div>
            </div>

            {{-- Filter Kelas (khusus siswa) --}}
            <div class="form-group mb-3 role-siswa role-field">
                <label for="kelas" class="form-label">Kelas</label>
                <select class="form-control select2" name="kelas" id="kelas">
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

            {{-- Pilih Siswa --}}
            <div class="form-group mb-3 role-siswa role-field">
                <label for="id_siswa" class="form-label">Nama Siswa</label>
                <select name="id_siswa" id="id_siswa" class="form-control select2">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach ($siswa as $s)
                        <option value="{{ $s->id_siswa }}" data-kelas="{{ $s->kelas }}">
                            {{ $s->nama_siswa }} ({{ $s->kelas }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Pilih Guru --}}
            <div class="form-group mb-3">
                <label for="id_guru" class="form-label">Nama Guru</label>
                <select name="id_guru" id="id_guru" class="form-control select2" required>
                    <option value="">-- Pilih Guru --</option>
                    @foreach ($guru as $g)
                        <option value="{{ $g->id_guru }}">{{ $g->nama_guru }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Pilih Mapel --}}
            <div class="form-group mb-3">
                <label for="id_mapel" class="form-label">Mata Pelajaran</label>
                <select name="id_mapel" class="form-control select2" required id="id_mapel">
                    <option value="">-- Pilih Mapel --</option>
                    @foreach ($mapel as $m)
                        <option value="{{ $m->id_mapel }}">{{ $m->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Ruangan --}}
            <div class="form-group mb-3">
                <label for="id_ruangan" class="form-label">Ruangan</label>
                <select name="id_ruangan" class="form-control select2" required id="id_ruangan">
                    <option value="">-- Pilih Ruangan --</option>
                    @foreach ($ruangan as $r)
                        <option value="{{ $r->id_ruangan }}">{{ $r->nama_ruangan }}</option>
                    @endforeach
                </select>
            </div>

            {{-- No Telp --}}
            <div class="form-group mb-3">
                <label class="form-label">Nomor HP</label>
                <input type="text" name="no_telp" class="form-control" required>
            </div>

            {{-- Jam KBM --}}
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
            <div class="form-group mb-3 role-siswa role-field">
                <label class="form-label">Jaminan</label>
                <input type="text" name="jaminan" class="form-control">
            </div>

            <hr>
            <h5 class="font-weight-bold text-secondary mb-3">Barang yang Dipinjam</h5>
            <div id="barang-wrapper">
                <div class="barang-group">
                    <div class="row">
                        <div class="col-md-5">
                            <select name="barang_id[]" class="form-control select2-barang" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach ($barang as $b)
                                    <option value="{{ $b->id_barang }}" data-stok="{{ $b->stok }}">
                                        {{ $b->nama_barang }} (Stok: {{ $b->stok }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input name="jumlah[]" type="number" class="form-control jumlah-input" placeholder="Jumlah" min="1" required>
                            <small class="text-muted stok-info d-block"></small>
                        </div>
                        <div class="col-md-4 ">
                            <button type="button" class="btn btn-danger btn-sm remove-barang">
                                <i class="bi bi-dash-circle"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" id="tambah-barang-btn" class="btn btn-sm btn-info mb-4">
                <i class="bi bi-plus-circle"></i> Tambah Barang
            </button>

            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Peminjaman
                </button>
                <a href="{{ route('petugas.peminjaman.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    console.log('Document ready!');

    // Initialize Select2 untuk form utama
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    // Initialize Select2 untuk barang
    initializeBarangSelect2();

    // Toggle Role
    function toggleRole(role) {
        console.log('Toggle role to:', role);

        if (role === 'siswa') {
            $('.role-siswa').show();
            $('#id_siswa').attr('required', true);
            $('input[name="jaminan"]').attr('required', true);
        } else if (role === 'guru') {
            $('.role-siswa').hide();
            $('#id_siswa').removeAttr('required').val('');
            $('input[name="jaminan"]').removeAttr('required').val('');
        }
    }

    // Set default ke siswa
    toggleRole('siswa');

    // Event handler untuk role change
    $('input[name="role"]').on('change', function() {
        console.log('Role changed to:', this.value);
        toggleRole(this.value);
    });

    // Filter siswa berdasarkan kelas
    const allSiswaOptions = $('#id_siswa option').clone();

    $('#kelas').on('change', function() {
        const selectedKelas = $(this).val();
        const $siswaSelect = $('#id_siswa');

        $siswaSelect.empty().append('<option value="">-- Pilih Siswa --</option>');

        if (selectedKelas) {
            allSiswaOptions.each(function() {
                if ($(this).data('kelas') === selectedKelas && $(this).val() !== '') {
                    $siswaSelect.append($(this).clone());
                }
            });
        } else {
            allSiswaOptions.each(function() {
                if ($(this).val() !== '') {
                    $siswaSelect.append($(this).clone());
                }
            });
        }
        $siswaSelect.trigger('change.select2');
    });

    // Event untuk tambah barang
    $('#tambah-barang-btn').on('click', function() {
        tambahBarang();
    });

    // Event untuk hapus barang - dengan event delegation
    $(document).on('click', '.remove-barang', function(e) {
        e.preventDefault();
        console.log('Remove clicked');

        if ($('.barang-group').length > 1) {
            $(this).closest('.barang-group').remove();
        } else {
            alert('Minimal harus ada 1 barang yang dipinjam!');
        }
    });

    // Validasi stok barang - event delegation
    $(document).on('change', 'select[name="barang_id[]"]', function() {
        const stok = $(this).find(':selected').data('stok') || 0;
        const $group = $(this).closest('.barang-group');
        const $stokInfo = $group.find('.stok-info');
        const $jumlahInput = $group.find('.jumlah-input');

        console.log('Barang selected, stok:', stok);

        if (stok > 0) {
            $stokInfo.removeClass('text-danger').addClass('text-info').text('Stok tersedia: ' + stok);
            $jumlahInput.attr('max', stok).prop('disabled', false);
        } else {
            $stokInfo.removeClass('text-info').addClass('text-danger').text('Stok habis!');
            $jumlahInput.val('').prop('disabled', true);
        }
    });

    // Validasi input jumlah - event delegation
    $(document).on('input', '.jumlah-input', function() {
        const max = parseInt($(this).attr('max')) || 0;
        const value = parseInt($(this).val()) || 0;

        console.log('Jumlah input:', value, 'Max:', max);

        if (value > max && max > 0) {
            $(this).val(max);
            alert('Jumlah tidak boleh melebihi stok (' + max + ')!');
        }
    });

    // Function untuk initialize Select2 pada barang
    function initializeBarangSelect2() {
        $('.select2-barang').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }

    // Function tambah barang - FIXED dengan template barang yang lebih baik
    function tambahBarang() {
        console.log('Tambah barang clicked');

        const $wrapper = $('#barang-wrapper');

        // Ambil data barang dari PHP
        const barangOptions = @json($barang->map(function($b) {
            return ['id' => $b->id_barang, 'nama' => $b->nama_barang, 'stok' => $b->stok];
        }));

        // Template untuk barang baru
        let newBarangHtml = `
            <div class="barang-group">
                <div class="row">
                    <div class="col-md-5">
                        <select name="barang_id[]" class="form-control select2-barang" required>
                            <option value="">-- Pilih Barang --</option>`;

        // Add options dari data barang
        barangOptions.forEach(function(barang) {
            newBarangHtml += `<option value="${barang.id}" data-stok="${barang.stok}">${barang.nama} (Stok: ${barang.stok})</option>`;
        });

        newBarangHtml += `
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input name="jumlah[]" type="number" class="form-control jumlah-input" placeholder="Jumlah" min="1" required>
                        <small class="text-muted stok-info d-block"></small>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-danger btn-sm remove-barang">
                            <i class="bi bi-dash-circle"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
        `;

        // Append ke wrapper
        $wrapper.append(newBarangHtml);

        // Initialize Select2 untuk elemen baru
        $wrapper.find('.barang-group:last .select2-barang').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        console.log('Barang added successfully');
    }

    // Expose function ke global scope
    window.tambahBarang = tambahBarang;
});
</script>
@endpush
