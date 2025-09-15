<?php

namespace App\Events;

use App\Models\Peminjaman;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class PeminjamanStatusUpdate implements ShouldBroadcast
{
    use SerializesModels;

    public $peminjaman;

    public function __construct(Peminjaman $peminjaman)
    {
       $peminjaman->mulai_kbm = \Carbon\Carbon::parse($peminjaman->mulai_kbm)->format('d/m/Y H:i');
    $peminjaman->selesai_kbm = \Carbon\Carbon::parse($peminjaman->selesai_kbm)->format('d/m/Y H:i');
        $this->peminjaman = $peminjaman;
    }

    public function broadcastOn()
    {
        return new Channel('gudang13');
    }

    public function broadcastAs()
    {
        return 'peminjaman.status.update';
    }

    public function broadcastWith()
    {
        return [
            'peminjaman' => $this->peminjaman->load(['siswa', 'guru', 'mapel', 'ruangan', 'detail.barang'])
        ];
    }
}
