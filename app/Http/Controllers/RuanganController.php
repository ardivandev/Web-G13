<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    // Menampilkan semua ruangan
    public function index(Request $request)
    {
        $query = Ruangan::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nama_ruangan', 'like', "%$search%");
        }

        $ruangan = $query->get();

        if (auth()->guard('admin')->check()) {
        return view('admin.ruangan.index', compact('ruangan'));
    } elseif (auth()->guard('petugas')->check()) {
        return view('petugas.ruangan.index', compact('ruangan'));
    }
    }

    // Menampilkan form tambah ruangan
    public function create()
    {
      return view('admin.ruangan.create');
    }

    // Menyimpan data ruangan baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:100',
        ]);

        Ruangan::create($request->all());

        return redirect()->route('admin.ruangan.index')->with('success', 'Data ruangan berhasil ditambahkan');
    }

    // Menampilkan form edit ruangan
    public function edit($id)
    {
      $ruangan = Ruangan::findOrFail($id);
      return view('admin.ruangan.edit', compact('ruangan'));
    }

    // Mengupdate data ruangan
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:100',
        ]);

        $ruangan = Ruangan::findOrFail($id);
        $ruangan->update($request->all());

        return redirect()->route('admin.ruangan.index')->with('success', 'Data ruangan berhasil diperbarui');
    }

    // Menghapus data ruangan
    public function destroy($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->delete();

        return redirect()->route('admin.ruangan.index')->with('success', 'Data ruangan berhasil dihapus');
    }
}
