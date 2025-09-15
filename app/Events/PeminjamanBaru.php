<?php

namespace App\Events;

use App\Models\Peminjaman;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PeminjamanBaru implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $peminjaman;

    /**
     * Create a new event instance.
     */
    public function __construct(Peminjaman $peminjaman)
    {
       $peminjaman->mulai_kbm = \Carbon\Carbon::parse($peminjaman->mulai_kbm)->format('d/m/Y H:i');
        $peminjaman->selesai_kbm = \Carbon\Carbon::parse($peminjaman->selesai_kbm)->format('d/m/Y H:i');
        $this->peminjaman = $peminjaman;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('gudang13'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'peminjaman.baru';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        try {
            // Ensure all relationships are loaded
            $this->peminjaman->load(['siswa', 'guru', 'mapel', 'ruangan', 'detail.barang']);

            return [
                'peminjaman' => [
                    'id_pinjam' => $this->peminjaman->id_pinjam,
                    'role' => $this->peminjaman->role,
                    'status' => $this->peminjaman->status,
                    'no_telp' => $this->peminjaman->no_telp,
                    'mulai_kbm' => $this->peminjaman->mulai_kbm,
                    'selesai_kbm' => $this->peminjaman->selesai_kbm,
                    'jaminan' => $this->peminjaman->jaminan,
                    'siswa' => $this->peminjaman->siswa ? [
                        'nama_siswa' => $this->peminjaman->siswa->nama_siswa,
                        'kelas' => $this->peminjaman->siswa->kelas ?? null,
                    ] : null,
                    'guru' => $this->peminjaman->guru ? [
                        'nama_guru' => $this->peminjaman->guru->nama_guru,
                    ] : null,
                    'mapel' => $this->peminjaman->mapel ? [
                        'nama_mapel' => $this->peminjaman->mapel->nama_mapel,
                    ] : null,
                    'ruangan' => $this->peminjaman->ruangan ? [
                        'nama_ruangan' => $this->peminjaman->ruangan->nama_ruangan,
                    ] : null,
                    'detail' => $this->peminjaman->detail->map(function ($detail) {
                        return [
                            'jumlah' => $detail->jumlah,
                            'barang' => [
                                'nama_barang' => $detail->barang->nama_barang ?? 'Unknown',
                            ],
                        ];
                    })->toArray(),
                ]
            ];
        } catch (\Exception $e) {
            \Log::error('Error in PeminjamanBaru broadcastWith: ' . $e->getMessage());

            // Return minimal data if error occurs
            return [
                'peminjaman' => [
                    'id_pinjam' => $this->peminjaman->id_pinjam ?? 0,
                    'role' => $this->peminjaman->role ?? 'unknown',
                    'status' => $this->peminjaman->status ?? 'menunggu',
                    'siswa' => null,
                    'guru' => null,
                    'mapel' => null,
                    'ruangan' => null,
                    'detail' => [],
                ]
            ];
        }
    }

    /**
     * Determine if this event should broadcast.
     */
    public function broadcastWhen(): bool
    {
        return $this->peminjaman !== null && $this->peminjaman->id_pinjam !== null;
    }
}
