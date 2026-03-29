<?php

namespace Tests\Feature\App\Channels;

use App\Events\OnlineCharactersUpdated;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Tests\TestCase;

class OnlineCharactersChannelTest extends TestCase {

    public function testOnlineCharactersChannel_ShouldAllowPublicAccess(): void {
        $event = new OnlineCharactersUpdated(collect(), 'TestGuild');

        $channel = $event->broadcastOn();

        $this->assertInstanceOf(Channel::class, $channel);
        $this->assertNotInstanceOf(PrivateChannel::class, $channel);
    }
}
