<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Events\StatusGudangUpdated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function toggleGudang(Request $request)
    {
        try {
            // Ambil atau buat setting baru
            $setting = Setting::firstOrCreate(
                [],  // tidak ada kondisi pencarian khusus
                ['status_gudang' => 'buka'] // default value jika tidak ada record
            );

            // Simpan status lama untuk logging
            $oldStatus = $setting->status_gudang;

            // Toggle status
            $newStatus = $setting->status_gudang === 'buka' ? 'tutup' : 'buka';
            $setting->status_gudang = $newStatus;
            $setting->save();

            // Clear cache jika menggunakan caching
            Cache::forget('status_gudang');

            // Log untuk debugging
            Log::info('Status gudang diubah', [
                'user_id' => auth('petugas')->id(),
                'user_name' => auth('petugas')->user()->nama_petugas ?? 'Unknown',
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'timestamp' => now()
            ]);

            // Broadcast event dengan delay kecil untuk memastikan database sudah terupdate
            broadcast(new StatusGudangUpdated($newStatus))->toOthers();

            // Log broadcast
            Log::info('Event StatusGudangUpdated berhasil di-broadcast', [
                'status' => $newStatus,
                'channel' => 'gudang13',
                'event' => 'status.gudang.updated'
            ]);

            // Response berbeda untuk AJAX dan non-AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'status' => $newStatus,
                    'message' => 'Status gudang berhasil diubah menjadi: ' . strtoupper($newStatus)
                ]);
            }

            return back()->with('success', 'Status gudang berhasil diubah menjadi: ' . strtoupper($newStatus));

        } catch (\Exception $e) {
            Log::error('Error mengubah status gudang', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth('petugas')->id() ?? null
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengubah status gudang'
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan saat mengubah status gudang');
        }
    }

    /**
     * Get current status gudang
     */
    public function getStatus()
    {
        try {
            $status = Cache::remember('status_gudang', 300, function () {
                $setting = Setting::first();
                return $setting ? $setting->status_gudang : 'buka';
            });

            return response()->json([
                'success' => true,
                'status' => $status
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting status gudang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'status' => 'buka' // default fallback
            ]);
        }
    }
}
