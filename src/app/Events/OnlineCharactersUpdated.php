<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class OnlineCharactersUpdated implements ShouldBroadcastNow {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly array $changes,
        public readonly array $removed,
        public readonly string $guildName,
    ) {}

    public function broadcastOn(): Channel {
        return new Channel('online-characters');
    }

    public function broadcastWith(): array {
        return [
            'changes' => $this->changes,
            'removed' => $this->removed,
        ];
    }
}
