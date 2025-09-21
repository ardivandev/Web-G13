<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\DetailPeminjaman;
use App\Events\PeminjamanBaru;
use App\Events\PeminjamanStatusUpdate;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Mapel;
use App\Models\Petugas;
use App\Models\Barang;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Peminjaman::with(['siswa', 'guru', 'mapel', 'ruangan', 'detail.barang']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('siswa', function($siswaQuery) use ($search) {
                    $siswaQuery->where('nama_siswa', 'like', "%{$search}%");
                })->orWhereHas('guru', function($guruQuery) use ($search) {
                    $guruQuery->where('nama_guru', 'like', "%{$search}%");
                })->orWhereHas('mapel', function($mapelQuery) use ($search) {
                    $mapelQuery->where('nama_mapel', 'like', "%{$search}%");
                })->orWhereHas('ruangan', function($ruanganQuery) use ($search) {
                    $ruanganQuery->where('nama_ruangan', 'like', "%{$search}%");
                })->orWhere('status', 'like', "%{$search}%");
            });
        }

        $peminjaman = $query->get();

        if (auth()->guard('admin')->check()) {
            return view('admin.peminjaman.index', compact('peminjaman'));
        } elseif (auth()->guard('petugas')->check()) {
            return view('petugas.peminjaman.index', compact('peminjaman'));
        } else {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }
    }

    public function createPengguna()
    {
        $barang = Barang::all();
        $guru = Guru::all();
        $siswa = Siswa::all();
        $mapel = Mapel::all();
        $ruangan = Ruangan::all();

        return view('pengguna.create_pinjaman', compact('barang', 'guru', 'siswa', 'mapel', 'ruangan'));
    }

    // STORE UNTUK PETUGAS - DENGAN EVENT BROADCASTING
    public function store(Request $request)
    {
        // Cek akses
        if (!auth()->guard('admin')->check() && !auth()->guard('petugas')->check()) {
            return redirect()->route('login')->with('error', 'Akses ditolak.');
        }

        // Validasi input
        $rules = [
            'id_siswa' => 'nullable|exists:tbl_siswa,id_siswa',
            'id_guru' => 'required|exists:tbl_guru,id_guru',
            'id_mapel' => 'required|exists:tbl_mapel,id_mapel',
            'id_ruangan' => 'required|exists:tbl_ruangan,id_ruangan',
            'no_telp' => 'required|string|max:20',
            'mulai_kbm' => 'required|date_format:H:i',
            'selesai_kbm' => 'required|date_format:H:i|after:mulai_kbm',
            'role' => 'required|in:siswa,guru',
            'barang_id' => 'required|array|min:1',
            'barang_id.*' => 'exists:tbl_barang,id_barang',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'required|integer|min:1',
            'jaminan' => 'nullable|string|max:100',
        ];
        $request->validate($rules);

        // Validasi stok barang
        $stokErrors = [];
        foreach ($request->barang_id as $index => $id_barang) {
            $barang = Barang::find($id_barang);
            $jumlah = $request->jumlah[$index] ?? 0;

            if (!$barang) continue;

            if ($jumlah > $barang->stok) {
                $stokErrors["jumlah.{$index}"] = "Stok tidak cukup untuk {$barang->nama_barang}. Tersedia: {$barang->stok}, diminta: {$jumlah}";
            }
        }

        if (!empty($stokErrors)) {
            return back()->withInput()->withErrors($stokErrors)->with('error', 'Stok barang tidak mencukupi!');
        }

        // Simpan data peminjaman
        DB::beginTransaction();
        try {
            $peminjamanData = [
                'id_siswa' => $request->role === 'siswa' ? $request->id_siswa : null,
                'id_guru' => $request->id_guru,
                'id_mapel' => $request->id_mapel,
                'id_ruangan' => $request->id_ruangan,
                'no_telp' => $request->no_telp,
                'mulai_kbm' => $request->mulai_kbm,
                'selesai_kbm' => $request->selesai_kbm,
                'jaminan' => $request->role === 'siswa' ? $request->jaminan : null,
                'role' => $request->role,
                'status' => 'Menunggu',
                'tanggal_pinjam' => now()->toDateString()
            ];

            $peminjaman = Peminjaman::create($peminjamanData);

            // Simpan detail barang
            foreach ($request->barang_id as $index => $id_barang) {
                DetailPeminjaman::create([
                    'id_pinjam' => $peminjaman->id_pinjam,
                    'id_barang' => $id_barang,
                    'jumlah' => $request->jumlah[$index],
                ]);
            }

            DB::commit();

            // Load relationships SETELAH commit untuk memastikan data tersimpan
            $peminjaman = Peminjaman::with(['siswa', 'guru', 'mapel', 'ruangan', 'detail.barang'])
                ->find($peminjaman->id_pinjam);

            // BROADCAST EVENT - FIXED VERSION
            try {
            \Log::info('Memulai broadcast PeminjamanBaru', [
                'id' => $peminjaman->id_pinjam,
                'role' => $peminjaman->role,
                'status' => $peminjaman->status
            ]);

            // Direct Pusher broadcast (lebih reliable)
            $this->broadcastDirectPusher($peminjaman, 'peminjaman.baru');

            // Laravel Event broadcast (fallback/opsional)
            event(new \App\Events\PeminjamanBaru($peminjaman));

            \Log::info('Event PeminjamanBaru berhasil di-broadcast', ['id' => $peminjaman->id_pinjam]);

        } catch (\Exception $e) {
            \Log::error('Gagal broadcast event PeminjamanBaru: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }

            $redirectRoute = auth()->guard('admin')->check() ? 'admin.peminjaman.index' : 'petugas.peminjaman.index';

            return redirect()->route($redirectRoute)->with('success', 'Peminjaman berhasil diajukan ');

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error creating peminjaman: ' . $e->getMessage());

            return back()->withInput()->with('error', 'Gagal menyimpan peminjaman: ' . $e->getMessage());
        }
    }

    // STORE UNTUK PENGGUNA - DENGAN EVENT BROADCASTING YANG DIPERBAIKI
    public function storePengguna(Request $request)
    {
        // Validasi berdasarkan role
        if ($request->role === 'siswa') {
            $request->validate([
                'id_siswa' => 'required|exists:tbl_siswa,id_siswa',
                'id_guru' => 'required|exists:tbl_guru,id_guru',
                'id_mapel' => 'required|exists:tbl_mapel,id_mapel',
                'id_ruangan' => 'required|exists:tbl_ruangan,id_ruangan',
                'no_telp' => 'required|string|max:20',
                'mulai_kbm' => 'required|date_format:H:i',
                'selesai_kbm' => 'required|date_format:H:i',
                'jaminan' => 'nullable|string|max:100',
                'role' => 'required|in:siswa,guru'
            ]);
        } else {
            $request->validate([
                'id_guru' => 'required|exists:tbl_guru,id_guru',
                'id_mapel' => 'required|exists:tbl_mapel,id_mapel',
                'id_ruangan' => 'required|exists:tbl_ruangan,id_ruangan',
                'no_telp' => 'required|string|max:20',
                'mulai_kbm' => 'required|date_format:H:i',
                'selesai_kbm' => 'required|date_format:H:i',
                'role' => 'required|in:siswa,guru'
            ]);
        }

        // Cek keranjang
        $cart = session('cart', []);
        if (empty($cart)) {
            return back()->with('error', 'Keranjang kosong. Silakan pilih barang terlebih dahulu.');
        }

        // Validasi stok barang di keranjang
        foreach ($cart as $key => $item) {
            $barang = Barang::find($item['id']);
            if (!$barang) {
                return back()->with('error', "Barang dengan ID {$item['id']} tidak ditemukan.");
            }

            if ($item['jumlah'] > $barang->stok) {
                return back()->with('error', "Jumlah {$barang->nama_barang} ({$item['jumlah']}) melebihi stok yang tersedia ({$barang->stok}).");
            }
        }

        DB::beginTransaction();
        try {
            // Buat peminjaman
            $peminjaman = Peminjaman::create([
                'id_siswa' => $request->role === 'siswa' ? $request->id_siswa : null,
                'id_guru' => $request->id_guru,
                'id_mapel' => $request->id_mapel,
                'id_ruangan' => $request->id_ruangan,
                'no_telp' => $request->no_telp,
                'mulai_kbm' => $request->mulai_kbm,
                'selesai_kbm' => $request->selesai_kbm,
                'jaminan' => $request->jaminan ?? null,
                'role' => $request->role,
                'status' => 'Menunggu',
                'tanggal_pinjam' => now()->toDateString()
            ]);

            // Detail peminjaman dari cart
            foreach ($cart as $key => $item) {
                DetailPeminjaman::create([
                    'id_pinjam' => $peminjaman->id_pinjam,
                    'id_barang' => $item['id'],
                    'jumlah' => $item['jumlah'] ?? 1,
                ]);
            }

            // Hapus keranjang
            session()->forget('cart');

            DB::commit();

            // Load relationships SETELAH commit
            $peminjaman = Peminjaman::with(['siswa', 'guru', 'mapel', 'ruangan', 'detail.barang'])
                ->find($peminjaman->id_pinjam);

            // BROADCAST EVENT - FIXED VERSION
            try {
                \Log::info('Memulai broadcast PeminjamanBaru dari pengguna', [
                    'id' => $peminjaman->id_pinjam,
                    'role' => $peminjaman->role
                ]);

                // Direct Pusher broadcast
                $this->broadcastDirectPusher($peminjaman, 'peminjaman.baru');

                // Laravel Event broadcast (fallback/opsional)
                event(new \App\Events\PeminjamanBaru($peminjaman));

                \Log::info('Event PeminjamanBaru dari pengguna berhasil di-broadcast', ['id' => $peminjaman->id_pinjam]);

            } catch (\Exception $e) {
                \Log::error('Gagal broadcast event PeminjamanBaru dari pengguna: ' . $e->getMessage());
            }

            return redirect()->route('pengguna.peminjaman.create')
                ->with('success', 'Peminjaman berhasil diajukan dan jangan tutup halaman ini sebelum menerima konfirmasi!')
                ->with('peminjaman', $peminjaman);

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mengajukan peminjaman: ' . $e->getMessage());
        }
    }

    // UPDATE STATUS dengan broadcast yang diperbaiki
    public function updateStatus(Request $request, $id)
{
    if (!auth()->guard('admin')->check() && !auth()->guard('petugas')->check()) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $raw = $request->input('status', '');
    $normalized = ucfirst(strtolower(trim($raw))); // -> " dipinjam " => "Dipinjam"
    $request->merge(['status' => $normalized]);

    $request->validate([
        'status' => 'required|in:Menunggu,Dipinjam,Ditolak,Selesai'
    ]);

    try {
        $peminjaman = Peminjaman::with(['detail.barang', 'siswa', 'guru', 'mapel', 'ruangan'])->findOrFail($id);
        $oldStatus = strtolower(trim($peminjaman->status));
        $newStatus = strtolower(trim($request->status));

        DB::beginTransaction();

        // CASE 1: STATUS BERUBAH DARI MENUNGGU KE DIPINJAM
        if ($newStatus === 'dipinjam' && $oldStatus === 'menunggu') {
            $stokTidakCukup = [];

            // Cek stok untuk setiap barang
            foreach ($peminjaman->detail as $detail) {
                $barang = Barang::find($detail->id_barang);

                if (!$barang) {
                    DB::rollBack();
                    return back()->with('error', "Barang dengan ID {$detail->id_barang} tidak ditemukan!");
                }

                if ($barang->stok < $detail->jumlah) {
                    $stokTidakCukup[] = [
                        'nama' => $barang->nama_barang,
                        'diminta' => $detail->jumlah,
                        'tersedia' => $barang->stok
                    ];
                }
            }

            if (!empty($stokTidakCukup)) {
                DB::rollBack();
                $pesanError = "Peminjaman tidak dapat disetujui karena stok tidak mencukupi:\n\n";
                foreach ($stokTidakCukup as $item) {
                    $pesanError .= "• {$item['nama']}: diminta {$item['diminta']}, tersedia {$item['tersedia']}\n";
                }
                return back()->with('error', $pesanError);
            }

            // Kurangi stok barang
            foreach ($peminjaman->detail as $detail) {
                $barang = Barang::find($detail->id_barang);
                $barang->decrement('stok', $detail->jumlah);
            }

            // Otomatis buat data pengembalian
            Pengembalian::create([
                'id_pinjam' => $peminjaman->id_pinjam,
                'tanggal_pengembalian' => null,
                'tanggal_harus_kembali' => now()->addDay()->format('Y-m-d'),
                'sanksi' => null
            ]);

            $successMessage = 'Peminjaman berhasil disetujui dan stok barang telah diperbarui.';
        }
        // CASE 2: STATUS BERUBAH DARI DIPINJAM KE STATUS LAIN (KEMBALIKAN STOK)
        elseif ($oldStatus === 'dipinjam' && $newStatus !== 'dipinjam') {
            // Kembalikan stok barang
            foreach ($peminjaman->detail as $detail) {
                $barang = Barang::find($detail->id_barang);
                if ($barang) {
                    $barang->increment('stok', $detail->jumlah);
                }
            }

            $successMessage = "Status berhasil diperbarui dan stok barang telah dikembalikan.";
        }
        // CASE 3: STATUS LAINNYA (TIDAK ADA PERUBAHAN STOK)
        else {
            $successMessage = 'Status berhasil diperbarui.';
        }

        // Update status peminjaman
        $peminjaman->status = ucfirst($newStatus);
        $peminjaman->save();

        DB::commit();

        // Load fresh data untuk broadcast
        $peminjaman = Peminjaman::with(['siswa', 'guru', 'mapel', 'ruangan', 'detail.barang'])
            ->find($peminjaman->id_pinjam);

        // Broadcast update status
        try {
            $this->broadcastDirectPusher($peminjaman, 'peminjaman.status.update');
            \Log::info('Event PeminjamanStatusUpdate berhasil di-broadcast', [
                'id' => $peminjaman->id_pinjam,
                'old_status' => $oldStatus,
                'new_status' => $peminjaman->status
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal broadcast status update: ' . $e->getMessage());
        }

        return back()->with('success', $successMessage);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error updating status: ' . $e->getMessage());
        return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
    }
}


    /**
     * METHOD BARU: Direct Pusher Broadcast (lebih reliable)
     */
    private function broadcastDirectPusher($peminjaman, $eventName)
    {
        try {
            // Initialize Pusher
            $pusher = new \Pusher\Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                [
                    'cluster' => env('PUSHER_APP_CLUSTER'),
                    'useTLS' => true,
                ]
            );

            // Prepare data dengan hati-hati untuk menghindari circular reference
            $broadcastData = [
                'peminjaman' => [
                    'id_pinjam' => $peminjaman->id_pinjam,
                    'role' => $peminjaman->role ?? 'unknown',
                    'status' => $peminjaman->status ?? 'menunggu',
                    'no_telp' => $peminjaman->no_telp,
                    'mulai_kbm' => $peminjaman->mulai_kbm,
                    'selesai_kbm' => $peminjaman->selesai_kbm,
                    'jaminan' => $peminjaman->jaminan,
                    'siswa' => $peminjaman->siswa ? [
                        'nama_siswa' => $peminjaman->siswa->nama_siswa,
                        'kelas' => $peminjaman->siswa->kelas ?? null,
                    ] : null,
                    'guru' => $peminjaman->guru ? [
                        'nama_guru' => $peminjaman->guru->nama_guru,
                    ] : null,
                    'mapel' => $peminjaman->mapel ? [
                        'nama_mapel' => $peminjaman->mapel->nama_mapel,
                    ] : null,
                    'ruangan' => $peminjaman->ruangan ? [
                        'nama_ruangan' => $peminjaman->ruangan->nama_ruangan,
                    ] : null,
                    'detail' => $peminjaman->detail ?
                        $peminjaman->detail->map(function ($detail) {
                            return [
                                'jumlah' => $detail->jumlah ?? 0,
                                'barang' => [
                                    'nama_barang' => $detail->barang->nama_barang ?? 'Unknown',
                                ],
                            ];
                        })->toArray() : [],
                ]
            ];

            // Broadcast ke channel
            $result = $pusher->trigger('gudang13', $eventName, $broadcastData);

            \Log::info('Direct Pusher broadcast successful', [
                'event' => $eventName,
                'id' => $peminjaman->id_pinjam,
                'result' => $result
            ]);

            return $result;

        } catch (\Exception $e) {
            \Log::error('Direct Pusher broadcast failed', [
                'event' => $eventName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Test Broadcast Method
     */
    public function testBroadcast()
    {
        try {
            $pusher = new \Pusher\Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                [
                    'cluster' => env('PUSHER_APP_CLUSTER'),
                    'useTLS' => true,
                ]
            );

            $result = $pusher->trigger('gudang13', 'test.event', [
                'message' => 'Test broadcast berhasil!',
                'timestamp' => now()->toISOString(),
                'server_time' => date('Y-m-d H:i:s')
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Broadcast test berhasil',
                'result' => $result,
                'config' => [
                    'app_id' => env('PUSHER_APP_ID'),
                    'key' => env('PUSHER_APP_KEY'),
                    'cluster' => env('PUSHER_APP_CLUSTER'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Broadcast test gagal: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Test Event dengan data peminjaman
     */
    public function testEvent($id = null)
    {
        try {
            if (!$id) {
                $peminjaman = Peminjaman::with(['siswa', 'guru', 'mapel', 'ruangan', 'detail.barang'])
                    ->latest()
                    ->first();
            } else {
                $peminjaman = Peminjaman::with(['siswa', 'guru', 'mapel', 'ruangan', 'detail.barang'])
                    ->findOrFail($id);
            }

            if (!$peminjaman) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada data peminjaman'
                ]);
            }

            // Test broadcast dengan data real
            $this->broadcastDirectPusher($peminjaman, 'test.peminjaman.baru');

            return response()->json([
                'status' => 'success',
                'message' => 'Event test berhasil dikirim',
                'peminjaman_id' => $peminjaman->id_pinjam,
                'data' => [
                    'role' => $peminjaman->role,
                    'status' => $peminjaman->status,
                    'siswa' => $peminjaman->siswa?->nama_siswa,
                    'guru' => $peminjaman->guru?->nama_guru,
                    'detail_count' => $peminjaman->detail->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event test gagal: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function create()
    {
        $barang = Barang::all();
        $guru = Guru::all();
        $siswa = Siswa::all();
        $mapel = Mapel::all();
        $ruangan = Ruangan::all();

        return view('petugas.peminjaman.create', compact('barang', 'guru', 'siswa', 'mapel', 'ruangan'));
    }

    public function edit($id)
    {
        $peminjaman = Peminjaman::with(['detail.barang'])->findOrFail($id);
        $barang = Barang::all();
        $guru = Guru::all();
        $siswa = Siswa::all();
        $mapel = Mapel::all();
        $ruangan = Ruangan::all();

        return view('petugas.peminjaman.edit', compact('peminjaman', 'barang', 'guru', 'siswa', 'mapel', 'ruangan'));
    }

    public function update(Request $request, $id)
    {
        $peminjaman = Peminjaman::with(['detail'])->findOrFail($id);

        $rules = [
            'id_siswa' => 'nullable|exists:tbl_siswa,id_siswa',
            'id_guru' => 'required|exists:tbl_guru,id_guru',
            'id_mapel' => 'required|exists:tbl_mapel,id_mapel',
            'id_ruangan' => 'required|exists:tbl_ruangan,id_ruangan',
            'no_telp' => 'required|string|max:20',
            'mulai_kbm' => 'required|date_format:H:i',
            'selesai_kbm' => 'required|date_format:H:i|after:mulai_kbm',
            'role' => 'required|in:siswa,guru',
            'barang_id' => 'required|array|min:1',
            'barang_id.*' => 'exists:tbl_barang,id_barang',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'required|integer|min:1',
            'jaminan' => 'nullable|string|max:100',
        ];
        $request->validate($rules);

        // Validasi stok barang
        $stokErrors = [];
        foreach ($request->barang_id as $index => $id_barang) {
            $barang = Barang::find($id_barang);
            $jumlah = $request->jumlah[$index] ?? 0;

            if (!$barang) continue;

            // Hitung stok sekarang + jumlah lama
            $jumlahLama = 0;
            $detail = $peminjaman->detail->firstWhere('id_barang', $id_barang);
            if ($detail) $jumlahLama = $detail->jumlah;

            if ($jumlah > ($barang->stok + $jumlahLama)) {
                $stokErrors["jumlah.{$index}"] = "Stok tidak cukup untuk {$barang->nama_barang}. Tersedia: {$barang->stok}, diminta: {$jumlah}";
            }
        }

        if (!empty($stokErrors)) {
            return back()->withInput()->withErrors($stokErrors)->with('error', 'Stok barang tidak mencukupi!');
        }

        DB::beginTransaction();
        try {
            $peminjaman->update([
                'id_siswa' => $request->role === 'siswa' ? $request->id_siswa : null,
                'id_guru' => $request->id_guru,
                'id_mapel' => $request->id_mapel,
                'id_ruangan' => $request->id_ruangan,
                'no_telp' => $request->no_telp,
                'mulai_kbm' => $request->mulai_kbm,
                'selesai_kbm' => $request->selesai_kbm,
                'jaminan' => $request->role === 'siswa' ? $request->jaminan : null,
                'role' => $request->role,
            ]);

            // Update detail barang
            $peminjaman->detail()->delete(); // hapus dulu
            foreach ($request->barang_id as $index => $id_barang) {
                DetailPeminjaman::create([
                    'id_pinjam' => $peminjaman->id_pinjam,
                    'id_barang' => $id_barang,
                    'jumlah' => $request->jumlah[$index],
                ]);
            }

            DB::commit();
            return redirect()->route('petugas.peminjaman.index')->with('success', 'Peminjaman berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui peminjaman: ' . $e->getMessage());
        }
    }

    // DESTROY METHOD - hapus peminjaman
    public function destroy($id)
{
    $peminjaman = Peminjaman::with(['detail.barang'])->findOrFail($id);

    DB::beginTransaction();
    try {
        // HANYA kembalikan stok jika status peminjaman adalah "Dipinjam"
        if (strtolower(trim($peminjaman->status)) === 'dipinjam') {
            foreach ($peminjaman->detail as $detail) {
                $barang = Barang::find($detail->id_barang);
                if ($barang) {
                    $barang->increment('stok', $detail->jumlah);
                    \Log::info("Stok dikembalikan untuk barang: {$barang->nama_barang}, jumlah: {$detail->jumlah}");
                }
            }
            $message = 'Peminjaman berhasil dihapus dan stok barang telah dikembalikan.';
        } else {
            // Jika status masih "Menunggu", "Ditolak", atau "Selesai", tidak perlu kembalikan stok
            $message = 'Peminjaman berhasil dihapus.';
            \Log::info("Peminjaman dihapus tanpa mengembalikan stok. Status: {$peminjaman->status}");
        }

        // Hapus detail peminjaman dan peminjaman
        $peminjaman->detail()->delete();
        $peminjaman->delete();

        DB::commit();

        // Tentukan route redirect berdasarkan guard
        $redirectRoute = auth()->guard('admin')->check() ?
            'admin.peminjaman.index' : 'petugas.peminjaman.index';

        return redirect()->route($redirectRoute)->with('success', $message);

    } catch (\Throwable $e) {
        DB::rollBack();
        \Log::error('Error deleting peminjaman: ' . $e->getMessage());
        return back()->with('error', 'Gagal menghapus peminjaman: ' . $e->getMessage());
    }
}

    // CART METHODS
    public function addToCart(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:tbl_barang,id_barang',
            'jumlah' => 'required|integer|min:1',
        ]);

        $barang = Barang::findOrFail($request->barang_id);

        if ($request->jumlah > $barang->stok) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah melebihi stok yang tersedia!'
            ], 400);
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$request->barang_id])) {
            $totalJumlah = $cart[$request->barang_id]['jumlah'] + $request->jumlah;

            if ($totalJumlah > $barang->stok) {
                return response()->json([
                    'success' => false,
                    'message' => 'Total jumlah melebihi stok yang tersedia!'
                ], 400);
            }

            $cart[$request->barang_id]['jumlah'] = $totalJumlah;
        } else {
            $cart[$request->barang_id] = [
                'id' => $barang->id_barang,
                'nama' => $barang->nama_barang,
                'stok' => $barang->stok,
                'jumlah' => $request->jumlah
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil ditambahkan ke keranjang',
            'cart_count' => array_sum(array_column($cart, 'jumlah'))
        ]);
    }

    public function removeFromCart($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $namaBarang = $cart[$id]['nama'];
            unset($cart[$id]);
            session()->put('cart', $cart);

            return response()->json([
                'success' => true,
                'message' => "Barang '{$namaBarang}' berhasil dihapus dari keranjang."
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Barang tidak ditemukan di keranjang.'
        ], 404);
    }

    public function getCartCount()
    {
        $cart = session('cart', []);
        $count = array_sum(array_column($cart, 'jumlah'));
        return response()->json(['count' => $count]);
    }

    // PDF METHODS - FIXED SESUAI STRUKTUR TABEL
    public function downloadBukti($id)
    {
        try {
            $peminjaman = Peminjaman::with(['siswa', 'guru', 'mapel', 'ruangan', 'detail.barang'])
                ->findOrFail($id);

            $data = [
                'peminjaman' => $peminjaman,
                'title' => 'Bukti Peminjaman'
            ];

            $pdf = Pdf::loadView('pdf.bukti_peminjaman', $data)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true
                ]);

            $filename = 'Bukti_Peminjaman_' . $peminjaman->id_pinjam . '_' . date('Y-m-d_H-i-s') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunduh bukti peminjaman: ' . $e->getMessage());
        }
    }

    public function downloadBuktiSession()
    {
        $peminjaman = session('peminjaman');
        $cart = session('cart', []);

        if (!$peminjaman) {
            return back()->with('error', 'Data peminjaman tidak ditemukan dalam session.');
        }

        $data = [
            'peminjaman' => $peminjaman,
            'cart' => $cart,
            'title' => 'Bukti Peminjaman'
        ];

        try {
            $pdf = Pdf::loadView('pdf.bukti_peminjaman_session', $data)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true
                ]);

            $filename = 'Bukti_Peminjaman_' . date('Y-m-d_H-i-s') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunduh bukti peminjaman: ' . $e->getMessage());
        }
    }

    public function debugSession()
    {
        $peminjaman = session('peminjaman');
        $cart = session('cart');

        return response()->json([
            'session_peminjaman' => $peminjaman,
            'session_cart' => $cart,
            'has_peminjaman' => $peminjaman ? true : false,
            'has_detail' => $peminjaman && isset($peminjaman->detail) ? true : false,
            'detail_count' => $peminjaman && isset($peminjaman->detail) ? count($peminjaman->detail) : 0,
        ]);
    }
}
