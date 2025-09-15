@auth('petugas')
<style>
    /* Sidebar */
    #accordionSidebar {
        background-color: #565477 !important;
        transition: all 0.3s ease-in-out;
        overflow-x: hidden;
    }

    /* Link default */
    #accordionSidebar .nav-item .nav-link {
        color: #fff;
        transition: all 0.2s ease-in-out;
        border-radius: 8px;
        padding: 10px 16px;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 8px;
        position: relative;
    }

    /* Ikon default */
    #accordionSidebar .nav-item .nav-link i {
        color: #cfcfea;
        transition: all 0.3s ease-in-out;
        font-size: 1.1rem;
    }

    /* Hover */
    #accordionSidebar .nav-item .nav-link:hover {
        background-color: #474163;
        color: #fff;
        padding-left: 20px;
    }
    #accordionSidebar .nav-item .nav-link:hover i {
        color: #ffffff;
    }

    /* Aktif */
    #accordionSidebar .nav-item.active > .nav-link {
        background-color: #2c2a40;
        color: #fff;
        font-weight: bold;
    }

    /* Ikon aktif */
    #accordionSidebar .nav-item.active > .nav-link i {
        color: #00d4ff;
        transform: scale(1.1);
    }

    /* Indikator neon di bawah */
    #accordionSidebar .nav-item.active > .nav-link::after {
        content: "";
        position: absolute;
        left: 10px;
        right: 10px;
        bottom: 4px;
        height: 3px;
        border-radius: 2px;
        background-color: #00d4ff;
        animation: neonSlide 0.3s ease-in-out;
    }

    /* Divider */
    #accordionSidebar .sidebar-divider {
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        margin: 0.8rem 0;
    }

    /* Heading kategori */
    .sidebar-heading {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #cfcfea;
        padding: 0 1rem;
        margin-top: 1rem;
    }

    /* Animasi expand submenu */
    .collapse.show {
        animation: expandMenu 0.3s ease-in-out;
    }
    @keyframes expandMenu {
        from { opacity: 0; transform: translateY(-10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Animasi underline neon */
    @keyframes neonSlide {
        from { width: 0; opacity: 0; }
        to   { width: 100%; opacity: 1; }
    }
</style>

<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
        <div class="sidebar-brand-text mx-3 bold">
            <h2>G13</h2>
        </div>
    </a>

    <hr class="sidebar-divider">

    <!-- Dashboard -->
    <li class="nav-item {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('petugas.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- Data Master -->
    <div class="sidebar-heading">Data Master</div>

    <li class="nav-item {{ request()->routeIs('siswa.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('petugas.siswa.index') }}">
            <i class="bi bi-person"></i> Data Siswa
        </a>
    </li>
    <li class="nav-item {{ request()->routeIs('guru.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('petugas.guru.index') }}">
            <i class="bi bi-person-gear"></i> Data Guru
        </a>
    </li>
    <li class="nav-item {{ request()->routeIs('mapel.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('petugas.mapel.index') }}">
            <i class="bi bi-journal-bookmark"></i> Data Mapel
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- Inventaris -->
    <div class="sidebar-heading">Inventaris</div>

    <li class="nav-item {{ request()->routeIs('barang.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('petugas.barang.index') }}">
            <i class="bi bi-box"></i> Data Barang
        </a>
    </li>
    <li class="nav-item {{ request()->routeIs('kategori.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('petugas.kategori.index') }}">
            <i class="bi bi-tags"></i> Data Kategori
        </a>
    </li>
    <li class="nav-item {{ request()->routeIs('ruangan.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('petugas.ruangan.index') }}">
            <i class="bi bi-building"></i> Data Ruangan
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- Transaksi -->
    <div class="sidebar-heading">Transaksi</div>

    <li class="nav-item {{ request()->routeIs('peminjaman.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('petugas.peminjaman.index') }}">
            <i class="bi bi-arrow-left-right"></i> Peminjaman
        </a>
    </li>
    <li class="nav-item {{ request()->routeIs('pengembalian.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('petugas.pengembalian.index') }}">
            <i class="bi bi-arrow-counterclockwise"></i> Pengembalian
        </a>
    </li>
</ul>
@endauth
