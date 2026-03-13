<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class OnlineCharactersUpdated implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Collection $characters,
        public readonly string $guildName,
    ) {}

    public function broadcastOn(): Channel {
        return new Channel("online-characters.{$this->guildName}");
    }

    public function broadcastWith(): array {
        return [
            'onlineCharacters' => $this->characters->toArray(),
        ];
    }
}
