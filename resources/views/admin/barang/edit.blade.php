@extends('layouts.admin.app')

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
     .btn-primary:active {
        background-color: #474163;
        border-color: #474163;
    }
</style>

<h1 class="h3 mb-4 text-gray-800">Edit Barang</h1>

<div class="card shadow mb-4 bg-white">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.barang.update', $barang->id_barang) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="nama_barang">Nama Barang</label>
                <input type="text" name="nama_barang" class="form-control"
                       value="{{ $barang->nama_barang }}"
                       placeholder="Masukkan nama barang" required>
            </div>

             {{-- ✅ Tambahan Spesifikasi --}}
            <div class="form-group">
                <label for="spesifikasi">Spesifikasi</label>
                <textarea name="spesifikasi" class="form-control" rows="4"
                          placeholder="Masukkan spesifikasi barang (opsional)">{{ $barang->spesifikasi }}</textarea>
            </div>

            <div class="form-group">
                <label for="stok">Stok</label>
                <input type="number" name="stok" class="form-control"
                       value="{{ $barang->stok }}"
                       placeholder="Masukkan jumlah stok" required>
            </div>

             <div class="form-group">
    <label for="id_kategori">Kategori</label>
    <select name="id_kategori" class="form-control">
        <option value="">-- Pilih Kategori --</option>
        @foreach ($kategori as $k)
            <option value="{{ $k->id_kategori }}"
                {{ $k->id_kategori == $barang->id_kategori ? 'selected' : '' }}>
                {{ $k->nama_kategori }}
            </option>
        @endforeach
    </select>
</div>

{{-- Gambar lama --}}
            <div class="form-group ">
                <label for="gambar_lama">Gambar Saat Ini</label>
                <div>
                    @if($barang->gambar)
                        <img src="{{ asset('images/barang/' . $barang->gambar) }}"
                             alt="{{ $barang->nama_barang }}"
                             width="150" height="150"
                             style="object-fit: cover; border-radius: 8px;">
                    @else
                        <p class="text-muted">Tidak ada gambar</p>
                    @endif
                </div>
            </div>

            {{-- Upload gambar baru --}}
            <div class="form-group">
                <label for="gambar">Upload Gambar Baru</label>
                <input type="file" name="gambar" class="form-control-file" accept="image/*">
                <small class="text-muted">Kosongkan jika tidak ingin mengganti gambar. Format: JPG, PNG, JPEG. Maks 2MB.</small>
            </div>


            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
             <a href="{{ route('admin.barang.index') }}" class="btn btn-primary">Kembali</a>
        </form>
    </div>
</div>
@endsection
