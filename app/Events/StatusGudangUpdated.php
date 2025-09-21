<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class StatusGudangUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $status;
    public $timestamp;
    public $broadcastQueue = 'default'; // Menggunakan queue untuk performance

    public function __construct($status)
    {
        $this->status = $status;
        $this->timestamp = now()->toISOString();

        // Log event creation
        Log::info('StatusGudangUpdated event created', [
            'status' => $status,
            'timestamp' => $this->timestamp
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        return new Channel('gudang13');
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs()
    {
        return 'status.gudang.updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith()
    {
        $data = [
            'status' => $this->status,
            'timestamp' => $this->timestamp,
            'message' => 'Status gudang berubah menjadi: ' . strtoupper($this->status)
        ];

        Log::info('Broadcasting data', $data);

        return $data;
    }

    /**
     * Determine if this event should broadcast.
     */
    public function broadcastWhen()
    {
        return in_array($this->status, ['buka', 'tutup']);
    }
}
