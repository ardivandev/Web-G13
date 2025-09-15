<?php

namespace App\Http\Controllers;
use App\Models\Pengembalian;
use App\Models\Peminjaman;
// use App\Models\DetailPeminjaman;
use Illuminate\Http\Request;
use Carbon\Carbon;


class PengembalianController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $pengembalian = Pengembalian::with([
            'peminjaman.siswa',
            'peminjaman.guru',
            'peminjaman.detail.barang'
        ])
        ->when($search, function ($query) use ($search) {
            $query->whereHas('peminjaman', function ($q) use ($search) {
                $q->whereHas('siswa', function ($siswaQuery) use ($search) {
                    $siswaQuery->where('nama_siswa', 'like', "%{$search}%");
                })->orWhereHas('guru', function ($guruQuery) use ($search) {
                    $guruQuery->where('nama_guru', 'like', "%{$search}%");
                });
            });
        })
        ->orderBy('id_kembali', 'desc')
        ->get();
        // Check guard dan return view yang sesuai
        if (auth()->guard('admin')->check()) {
            return view('admin.pengembalian.index', compact('pengembalian'));
        } elseif (auth()->guard('petugas')->check()) {
            return view('petugas.pengembalian.index', compact('pengembalian'));
        }

        // Fallback jika tidak ada guard yang cocok
        return redirect()->route('login');
    }

    public function create()
    {
        // Ambil peminjaman yang statusnya 'Dipinjam' dan belum ada di tabel pengembalian
        $peminjaman = Peminjaman::with(['siswa', 'guru', 'detail.barang'])
            ->where('status', 'Dipinjam')
            ->whereDoesntHave('pengembalian')
            ->get();

        // Check guard dan return view yang sesuai
        if (auth()->guard('admin')->check()) {
            return view('admin.pengembalian.create', compact('peminjaman'));
        } elseif (auth()->guard('petugas')->check()) {
            return view('petugas.pengembalian.create', compact('peminjaman'));
        }

        return redirect()->route('login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pinjam' => 'required|exists:tbl_peminjaman,id_pinjam',
            'tanggal_pengembalian' => 'required|date',
            'tanggal_harus_kembali' => 'required|date',
            'sanksi' => 'nullable|string|max:255',
        ]);

        try {
            // Buat record pengembalian
            Pengembalian::create([
                'id_pinjam' => $request->id_pinjam,
                'tanggal_pengembalian' => $request->tanggal_pengembalian,
                'tanggal_harus_kembali' => $request->tanggal_harus_kembali,
                'sanksi' => $request->sanksi,
            ]);

            // Update status peminjaman menjadi 'Selesai' dan kembalikan stok
            $peminjaman = Peminjaman::with('detail.barang')->findOrFail($request->id_pinjam);

            // Kembalikan stok barang
            foreach ($peminjaman->detail as $detail) {
                $detail->barang->increment('stok', $detail->jumlah);
            }

            $peminjaman->update(['status' => 'Selesai']);

            // Tentukan route redirect berdasarkan guard
            $redirectRoute = auth()->guard('admin')->check() ? 'admin.pengembalian.index' : 'petugas.pengembalian.index';

            return redirect()->route($redirectRoute)
                           ->with('success', 'Data pengembalian berhasil ditambahkan!');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambah data pengembalian: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function edit($id)
    {
        $pengembalian = Pengembalian::with(['peminjaman.siswa', 'peminjaman.guru'])
                                  ->findOrFail($id);

        $peminjaman = Peminjaman::with(['siswa', 'guru'])->get();

        // Check guard dan return view yang sesuai
        if (auth()->guard('admin')->check()) {
            return view('admin.pengembalian.edit', compact('pengembalian', 'peminjaman'));
        } elseif (auth()->guard('petugas')->check()) {
            return view('petugas.pengembalian.edit', compact('pengembalian', 'peminjaman'));
        }

        return redirect()->route('login');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_pinjam' => 'required|exists:tbl_peminjaman,id_pinjam',
            'tanggal_pengembalian' => 'required|date',
            'tanggal_harus_kembali' => 'required|date',
            'sanksi' => 'nullable|string|max:255',
        ]);

        try {
            $pengembalian = Pengembalian::findOrFail($id);
            $oldIdPinjam = $pengembalian->id_pinjam;
            $newIdPinjam = $request->id_pinjam;

            // Jika id_pinjam berubah, kita perlu update status peminjaman
            if ($oldIdPinjam != $newIdPinjam) {
                // Kembalikan status peminjaman lama ke 'Dipinjam'
                $oldPeminjaman = Peminjaman::with('detail.barang')->find($oldIdPinjam);
                if ($oldPeminjaman) {
                    // Kurangi stok karena peminjaman kembali aktif
                    foreach ($oldPeminjaman->detail as $detail) {
                        $detail->barang->decrement('stok', $detail->jumlah);
                    }
                    $oldPeminjaman->update(['status' => 'Dipinjam']);
                }

                // Update status peminjaman baru ke 'Selesai'
                $newPeminjaman = Peminjaman::with('detail.barang')->findOrFail($newIdPinjam);
                // Kembalikan stok dari peminjaman baru
                foreach ($newPeminjaman->detail as $detail) {
                    $detail->barang->increment('stok', $detail->jumlah);
                }
                $newPeminjaman->update(['status' => 'Selesai']);
            }

            $pengembalian->update([
                'id_pinjam' => $request->id_pinjam,
                'tanggal_pengembalian' => $request->tanggal_pengembalian,
                'tanggal_harus_kembali' => $request->tanggal_harus_kembali,
                'sanksi' => $request->sanksi,
            ]);

            // Tentukan route redirect berdasarkan guard
            $redirectRoute = auth()->guard('admin')->check() ? 'admin.pengembalian.index' : 'petugas.pengembalian.index';

            return redirect()->route($redirectRoute)
                           ->with('success', 'Data pengembalian berhasil diperbarui!');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui data pengembalian: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $pengembalian = Pengembalian::with('peminjaman.detail.barang')->findOrFail($id);

            // Kembalikan status peminjaman ke 'Dipinjam' jika pengembalian dihapus
            if ($pengembalian->peminjaman) {
                $peminjaman = $pengembalian->peminjaman;

                // Kurangi lagi stok barang karena pengembalian dibatalkan
                foreach ($peminjaman->detail as $detail) {
                    $detail->barang->decrement('stok', $detail->jumlah);
                }

                $peminjaman->update(['status' => 'Dipinjam']);
            }

            $pengembalian->delete();

            // Tentukan route redirect berdasarkan guard
            $redirectRoute = auth()->guard('admin')->check() ? 'admin.pengembalian.index' : 'petugas.pengembalian.index';

            return redirect()->route($redirectRoute)
                           ->with('success', 'Data pengembalian berhasil dihapus!');

        } catch (\Exception $e) {
            $redirectRoute = auth()->guard('admin')->check() ? 'admin.pengembalian.index' : 'petugas.pengembalian.index';

            return redirect()->route($redirectRoute)
                           ->with('error', 'Gagal menghapus data pengembalian: ' . $e->getMessage());
        }
    }

// Tambahkan method ini di PengembalianController
public function complete(Request $request, $id)
{
    if (!auth()->guard('admin')->check() && !auth()->guard('petugas')->check()) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    try {
        $pengembalian = Pengembalian::with('peminjaman.detail.barang')->findOrFail($id);

        // Pastikan peminjaman masih dalam status 'Dipinjam'
        if (strtolower($pengembalian->peminjaman->status) !== 'dipinjam') {
            return back()->with('error', 'Peminjaman tidak dalam status dipinjam!');
        }

        // Jika belum ada tanggal pengembalian, set ke hari ini
        if (!$pengembalian->tanggal_pengembalian) {
            $tanggalPengembalian = now();

            // Hitung sanksi jika terlambat
            $sanksi = null;
            if ($tanggalPengembalian->gt(Carbon::parse($pengembalian->tanggal_harus_kembali))) {
                $hariTerlambat = $tanggalPengembalian->diffInDays($pengembalian->tanggal_harus_kembali);
                $sanksi = "Terlambat {$hariTerlambat} hari";
            }

            // Update data pengembalian
            $pengembalian->update([
                'tanggal_pengembalian' => $tanggalPengembalian->format('Y-m-d'),
                'sanksi' => $sanksi
            ]);
        }

        // Kembalikan stok barang
        foreach ($pengembalian->peminjaman->detail as $detail) {
            $detail->barang->increment('stok', $detail->jumlah);
        }

        // Update status peminjaman ke 'Selesai'
        $pengembalian->peminjaman->update(['status' => 'Selesai']);

        // Tentukan route redirect berdasarkan guard
        $redirectRoute = auth()->guard('admin')->check() ? 'admin.pengembalian.index' : 'petugas.pengembalian.index';

        return redirect()->route($redirectRoute)
                       ->with('success', 'Peminjaman berhasil diselesaikan!');

    } catch (\Exception $e) {
        return back()->with('error', 'Gagal menyelesaikan peminjaman: ' . $e->getMessage());
    }
}
}