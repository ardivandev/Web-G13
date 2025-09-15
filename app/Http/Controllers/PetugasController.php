<?php

namespace App\Http\Controllers;

use App\Models\Petugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PetugasController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $petugas = Petugas::when($search, function ($query, $search) {
            return $query->where('nama_petugas', 'like', "%$search%")
                         ->orWhere('email', 'like', "%$search%");
        })->get();

        return view('admin.petugas.index', compact('petugas'));
    }

    public function create()
    {
        return view('admin.petugas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_petugas' => 'required|string|max:100',
            'username' => 'required|unique:tbl_petugas,username',
            'email' => 'required|email|max:100|unique:tbl_petugas,email',
            'password' => 'required|min:6',
        ]);

        Petugas::create([
            'nama_petugas'   => $request->nama_petugas,
            'username'       => $request->username,
            'email'          => $request->email,
            'password'       => Hash::make($request->password), // login
            'password_asli'  => $request->password,             // tampil di index
        ]);

        return redirect()->route('admin.petugas.index')->with('success', 'Data petugas berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $petugas = Petugas::findOrFail($id);
        return view('admin.petugas.edit', compact('petugas'));
    }

    public function update(Request $request, $id)
    {
        $petugas = Petugas::findOrFail($id);

        $request->validate([
            'nama_petugas' => 'required|string|max:100',
            'username' => 'required|unique:tbl_petugas,username,' . $petugas->id_petugas . ',id_petugas',
            'email' => 'required|email|max:100|unique:tbl_petugas,email,' . $petugas->id_petugas . ',id_petugas',
        ]);

        $data = [
            'nama_petugas' => $request->nama_petugas,
            'username'     => $request->username,
            'email'        => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            $data['password_asli'] = $request->password;
        }

        $petugas->update($data);

        return redirect()->route('admin.petugas.index')->with('success', 'Data petugas berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Petugas::findOrFail($id)->delete();
        return redirect()->route('admin.petugas.index')->with('success', 'Data petugas berhasil dihapus!');
    }
}
