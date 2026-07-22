<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MikrotikTrafficUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $serverId;
    public $upload;
    public $download;
    public $interface;

    public function __construct($serverId, $upload, $download, $interface)
    {
        $this->serverId = $serverId;
        $this->upload = (int) $upload;
        $this->download = (int) $download;
        $this->interface = $interface;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('mikrotik-traffic.' . $this->serverId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'TrafficUpdated';
    }
}
