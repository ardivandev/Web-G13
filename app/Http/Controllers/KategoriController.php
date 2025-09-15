<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $query = Kategori::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nama_kategori', 'like', "%$search%");
        }

        $kategori = $query->get();

        if (auth()->guard('admin')->check()) {
            return view('admin.kategori.index', compact('kategori'));
        } elseif (auth()->guard('petugas')->check()) {
            return view('petugas.kategori.index', compact('kategori'));
        }
    }

    public function create()
    {
        if (auth()->guard('admin')->check()) {
            return view('admin.kategori.create');
        } elseif (auth()->guard('petugas')->check()) {
            return view('petugas.kategori.create');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:tbl_kategori_barang,nama_kategori'
        ]);

        Kategori::create($validated);

        if (auth()->guard('admin')->check()) {
            return redirect()
                ->route('admin.kategori.index')
                ->with('success', 'Data kategori berhasil ditambahkan.');
        } elseif (auth()->guard('petugas')->check()) {
            return redirect()
                ->route('petugas.kategori.index')
                ->with('success', 'Data kategori berhasil ditambahkan.');
        }
    }

    public function edit(Kategori $kategori)
    {
        if (auth()->guard('admin')->check()) {
            return view('admin.kategori.edit', compact('kategori'));
        } elseif (auth()->guard('petugas')->check()) {
            return view('petugas.kategori.edit', compact('kategori'));
        }
    }

    public function update(Request $request, Kategori $kategori)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:tbl_kategori_barang,nama_kategori,' . $kategori->id_kategori . ',id_kategori'
        ]);

        $kategori->update($validated);

        if (auth()->guard('admin')->check()) {
            return redirect()
                ->route('admin.kategori.index')
                ->with('success', 'Data kategori berhasil diperbarui.');
        } elseif (auth()->guard('petugas')->check()) {
            return redirect()
                ->route('petugas.kategori.index')
                ->with('success', 'Data kategori berhasil diperbarui.');
        }
    }

    public function destroy(Kategori $kategori)
    {
        $kategori->delete();

        if (auth()->guard('admin')->check()) {
            return redirect()
                ->route('admin.kategori.index')
                ->with('success', 'Data kategori berhasil dihapus.');
        } elseif (auth()->guard('petugas')->check()) {
            return redirect()
                ->route('petugas.kategori.index')
                ->with('success', 'Data kategori berhasil dihapus.');
        }
    }
}
