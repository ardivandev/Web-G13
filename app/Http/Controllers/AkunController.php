<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Petugas;

class AkunController extends Controller
{
    public function index()
    {
        if (auth('admin')->check()) {
            $user = auth('admin')->user();
            return view('admin.akun.index', compact('user'));
        } elseif (auth('petugas')->check()) {
            $user = auth('petugas')->user();
            return view('petugas.akun.index', compact('user'));
        }

        // Fallback if no guard is authenticated
        return redirect()->route('login');
    }

public function updatePassword(Request $request)
{
    if (auth('admin')->check()) {
        $user = auth('admin')->user();
        $guard = 'admin';
    } elseif (auth('petugas')->check()) {
        $user = auth('petugas')->user();
        $guard = 'petugas';
    } else {
        return redirect()->route('login');
    }

    $request->validate([
        'password_baru' => 'required|min:6|confirmed',
    ]);

    $user->password = Hash::make($request->password_baru);
    $user->save();

    auth($guard)->logout();

    return redirect()->route('login')->with('success', 'Password berhasil diperbarui, silakan login kembali.');
}

}
