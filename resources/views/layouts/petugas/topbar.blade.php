<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Tombol Buka/Tutup Gudang -->
    @php
        $statusGudang = \App\Models\Setting::first()->status_gudang ?? 'buka';
    @endphp
    <form action="{{ route('petugas.gudang.toggle') }}" method="POST" class="form-inline mr-auto">
        @csrf
        <button type="submit"
            class="btn btn-sm {{ $statusGudang == 'buka' ? 'btn-danger' : 'btn-success' }}"
            id="btn-gudang-toggle">
            {{ $statusGudang == 'buka' ? 'Tutup Gudang' : 'Buka Gudang' }}
        </button>
    </form>

    <!-- Menu Kanan -->
    <ul class="navbar-nav ml-auto align-items-center">

        <!-- Nama Admin -->
        <li class="nav-item mr-3">
            <span class="nav-link">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    Hallo, {{ Auth::guard('petugas')->user()->nama_petugas }}
                </span>
                <i class="fas fa-user-circle fa-lg"></i>
            </span>
        </li>

        <!-- Tombol Logout -->
        <li class="nav-item">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </button>
            </form>
        </li>
    </ul>
</nav>
