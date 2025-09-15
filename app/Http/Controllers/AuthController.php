<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Coba login sebagai Admin
        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard')
                ->with('success', 'Login berhasil sebagai Admin!');
        }

        // Coba login sebagai Petugas
       if (Auth::guard('petugas')->attempt($credentials)) {
    $request->session()->regenerate();
    $petugas = Auth::guard('petugas')->user(); // <--- ini ambil data petugas
    return redirect()->route('petugas.dashboard')
        ->with('success', 'Selamat datang, ' . $petugas->nama_petugas . '!');
}

        // Kalau dua-duanya gagal
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        Auth::guard('petugas')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
