<?php

namespace App\Http\Controllers;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function toggleGudang()
    {
        $setting = Setting::first() ?? new Setting();
        $setting->status_gudang = $setting->status_gudang === 'buka' ? 'tutup' : 'buka';
        $setting->save();

        return back()->with('success', 'Status gudang berhasil diubah menjadi: ' . $setting->status_gudang);
    }
}
