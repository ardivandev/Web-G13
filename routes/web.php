<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\{
    AuthController,
    BarangController,
    SiswaController,
    PeminjamanController,
    PengembalianController,
    DashboardController,
    KategoriController,
    PetugasController,
    SettingController,
    MapelController,
    RuanganController,
    GuruController,
    PetugasDashboardController,
    LaporanController
};
use App\Models\Barang;
use App\Models\Setting;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('peminjaman-channel', function ($user) {
    return true;
});
// ===========================
// Root redirect -> intro
// ===========================
Route::get('/', fn() => redirect('/welcome'));
Route::view('/welcome', 'welcome');

// ===========================
// Login / Logout
// ===========================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Debug route (remove in production)
Route::get('/debug-peminjaman', function() {
    $peminjaman = session('peminjaman');
    $cart = session('cart');

    dd([
        'has_session_peminjaman' => $peminjaman ? true : false,
        'peminjaman_data' => $peminjaman,
        'has_detail' => $peminjaman && isset($peminjaman->detail) ? true : false,
        'detail_count' => $peminjaman && isset($peminjaman->detail) ? count($peminjaman->detail) : 0,
        'detail_raw' => $peminjaman && isset($peminjaman->detail) ? $peminjaman->detail->toArray() : null,
        'cart_data' => $cart,
        'cart_count' => $cart ? count($cart) : 0
    ]);
});


// Pengguna Routes (siswa/guru umum, tanpa login admin/petugas)
Route::prefix('pengguna')->name('pengguna.')->group(function () {
    // halaman utama + pencarian
    Route::get('/index', function (Request $request) {
        try {
            $query = $request->input('q'); // ambil kata kunci pencarian

            $barang = Barang::with('kategori')
                ->when($query, function ($qBuilder) use ($query) {
                    $qBuilder->where('nama_barang', 'like', "%{$query}%");
                })
                ->get();

            $statusGudang = Setting::first()?->status_gudang ?? 'buka';

            return view('pengguna.index', compact('barang', 'statusGudang'));
        } catch (\Exception $e) {
            return redirect('/welcome')->with('error', 'Terjadi kesalahan saat memuat data: ' . $e->getMessage());
        }
    })->name('index');

    // daftar peminjaman
    Route::get('/peminjaman', [PeminjamanController::class, 'indexPengguna'])
        ->name('peminjaman.index');

    // form create peminjaman
    Route::get('/create_pinjaman', [PeminjamanController::class, 'createPengguna'])
        ->name('peminjaman.create');

    // halaman tentang
    Route::get('/tentang', function () {
        return view('pengguna.tentang');
    });

    Route::post('/cart/add', [PeminjamanController::class, 'addToCart'])
        ->name('peminjaman.add');

    Route::delete('/cart/remove/{id}', [PeminjamanController::class, 'removeFromCart'])
        ->name('peminjaman.remove');

    Route::post('/peminjaman/store', [PeminjamanController::class, 'storePengguna'])
        ->name('peminjaman.store');

    Route::get('/peminjaman/count', [PeminjamanController::class, 'getCartCount'])
        ->name('peminjaman.count');

    Route::get('/peminjaman/download/{id}', [PeminjamanController::class, 'downloadBukti'])->name('peminjaman.download');
    Route::get('/peminjaman/download-session', [PeminjamanController::class, 'downloadBuktiSession'])->name('peminjaman.download.session');
});


// Admin Routes
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Toggle Gudang
    Route::post('/toggle-gudang', [SettingController::class, 'toggleGudang'])->name('gudang.toggle');

    // ========== Master Data ==========
    Route::resource('siswa', SiswaController::class)->except(['show']);

    // Guru routes with specific ordering
    Route::delete('guru/destroy-all', [GuruController::class, 'destroyAll'])->name('guru.destroyAll');
    Route::post('guru/import', [GuruController::class, 'import'])->name('guru.import');
    Route::get('guru/template', [GuruController::class, 'template'])->name('guru.template');
    Route::resource('guru', GuruController::class)->except(['show']);

    Route::resource('petugas', PetugasController::class)->except(['show']);
    Route::resource('barang', BarangController::class)->except(['show']);
    Route::resource('mapel', MapelController::class)->except(['show']);
    Route::resource('kategori', KategoriController::class)->except(['show']);
    Route::resource('ruangan', RuanganController::class)->except(['show']);

    //laporan
    Route::prefix('laporan')->name('laporan.')->group(function() {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/barang-sering', [LaporanController::class, 'barangSering'])->name('barang-sering');
        Route::get('/export-excel', [LaporanController::class, 'exportExcel'])->name('export-excel');
        Route::get('/export-pdf', [LaporanController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/chart-data', [LaporanController::class, 'getChartData'])->name('chart-data');
    });

    // ========== PEMINJAMAN ROUTES - HARUS DI ATAS RESOURCE ==========
    // Route khusus update status HARUS di atas resource route
    Route::patch('/peminjaman/{id}/status', [PeminjamanController::class, 'updateStatus'])
        ->name('peminjaman.updateStatus');

    Route::get('/pengembalian', [PengembalianController::class, 'index'])->name('pengembalian.index');
    Route::patch('/pengembalian/{id}/complete', [PengembalianController::class, 'complete'])->name('pengembalian.complete');

    // Resource route peminjaman
    Route::resource('peminjaman', PeminjamanController::class)->except(['show']);

    // Resource route pengembalian
    Route::resource('pengembalian', PengembalianController::class)->except(['show']);

    // Import/Template routes
    Route::post('siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
    Route::get('siswa/template', [SiswaController::class, 'template'])->name('siswa.template');
    Route::post('mapel/import', [MapelController::class, 'import'])->name('mapel.import');
    Route::get('mapel/template', [MapelController::class, 'template'])->name('mapel.template');
    Route::post('barang/import', [BarangController::class, 'import'])->name('barang.import');
    Route::get('barang/template', [BarangController::class, 'template'])->name('barang.template');

    // Route khusus destroy all (harus di atas middleware group)
    Route::delete('siswa/destroy-all', [SiswaController::class, 'destroyAll'])->name('siswa.destroyAll');
    Route::delete('guru/destroy-all', [GuruController::class, 'destroyAll'])->name('guru.destroyAll');
});


// Petugas Routes
Route::middleware('auth:petugas')->prefix('petugas')->name('petugas.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [PetugasDashboardController::class, 'index'])->name('dashboard');

    // Toggle Gudang
    Route::post('/toggle-gudang', [SettingController::class, 'toggleGudang'])->name('gudang.toggle');

    // ========== Master Data ==========
    Route::resource('siswa', SiswaController::class)->except(['show']);

    // Guru routes for petugas
    Route::resource('guru', GuruController::class)->except(['show']);

    Route::resource('barang', BarangController::class)->except(['show']);
    Route::resource('kategori', KategoriController::class)->except(['show']);
    Route::resource('ruangan', RuanganController::class)->except(['show']);
    Route::resource('mapel', MapelController::class)->except(['show']);

    // ========== PEMINJAMAN ROUTES - HARUS DI ATAS RESOURCE ==========
    // Route khusus update status HARUS di atas resource route
    Route::patch('/peminjaman/{id}/status', [PeminjamanController::class, 'updateStatus'])
        ->name('peminjaman.updateStatus');
       Route::get('/pengembalian', [PengembalianController::class, 'index'])->name('pengembalian.index');
    Route::patch('/pengembalian/{id}/complete', [PengembalianController::class, 'complete'])->name('pengembalian.complete');

    // Resource route peminjaman
    Route::resource('peminjaman', PeminjamanController::class)->except(['show']);

    // Resource route pengembalian
    Route::resource('pengembalian', PengembalianController::class)->except(['show']);

    // barang import
    Route::post('barang/import', [BarangController::class, 'import'])->name('barang.import');
    Route::get('barang/template', [BarangController::class, 'template'])->name('barang.template');
});

// ===========================
// Error Handling Routes
// ===========================
Route::fallback(function () {
    return redirect('/welcome')->with('error', 'Halaman yang Anda cari tidak ditemukan.');
});
