<?php
namespace App\Events;

use App\Models\Peminjaman;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class PeminjamanDibuat implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $peminjaman;

    public function __construct(Peminjaman $peminjaman)
    {
       $peminjaman->mulai_kbm = \Carbon\Carbon::parse($peminjaman->mulai_kbm)->format('d/m/Y H:i');
    $peminjaman->selesai_kbm = \Carbon\Carbon::parse($peminjaman->selesai_kbm)->format('d/m/Y H:i');
        $this->peminjaman = $peminjaman;
    }

    public function broadcastOn()
    {
        return new Channel('gudang13'); // Sesuai channel petugas
    }

    public function broadcastAs()
    {
        return 'peminjaman.dibuat';
    }
}
