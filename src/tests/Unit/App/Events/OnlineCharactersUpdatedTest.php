<?php

namespace Tests\Unit\App\Events;

use App\Events\OnlineCharactersUpdated;
use Illuminate\Broadcasting\Channel;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class OnlineCharactersUpdatedTest extends TestCase {

    public function testBroadcastOn_ShouldReturnPublicChannelWithGuildName(): void {
        $event = new OnlineCharactersUpdated(collect([]), 'TestGuild');

        $channel = $event->broadcastOn();

        $this->assertInstanceOf(Channel::class, $channel);
        $this->assertEquals('online-characters.TestGuild', $channel->name);
    }

    public function testBroadcastOn_ShouldIncludeGuildNameInChannelName(): void {
        $event = new OnlineCharactersUpdated(collect([]), 'AnotherGuild');

        $channel = $event->broadcastOn();

        $this->assertEquals('online-characters.AnotherGuild', $channel->name);
    }

    public function testBroadcastWith_ShouldReturnOnlineCharactersKey(): void {
        $character = ['name' => 'TestChar', 'is_online' => true, 'vocation' => 'Knight', 'level' => 100];
        $event = new OnlineCharactersUpdated(collect([$character]), 'TestGuild');

        $data = $event->broadcastWith();

        $this->assertArrayHasKey('onlineCharacters', $data);
        $this->assertCount(1, $data['onlineCharacters']);
        $this->assertEquals('TestChar', $data['onlineCharacters'][0]['name']);
    }

    public function testBroadcastWith_WhenEmptyCollection_ShouldReturnEmptyOnlineCharacters(): void {
        $event = new OnlineCharactersUpdated(collect([]), 'TestGuild');

        $data = $event->broadcastWith();

        $this->assertArrayHasKey('onlineCharacters', $data);
        $this->assertEmpty($data['onlineCharacters']);
    }

    public function testBroadcastWith_ShouldIncludeAllCharacterFields(): void {
        $character = [
            'name' => 'TestChar',
            'is_online' => true,
            'vocation' => 'Knight',
            'level' => 100,
        ];
        $event = new OnlineCharactersUpdated(collect([$character]), 'TestGuild');

        $data = $event->broadcastWith();

        $charData = $data['onlineCharacters'][0];
        $this->assertEquals('TestChar', $charData['name']);
        $this->assertEquals('Knight', $charData['vocation']);
        $this->assertEquals(100, $charData['level']);
        $this->assertTrue($charData['is_online']);
    }

    public function testBroadcastWith_ShouldPreserveMultipleCharacters(): void {
        $characters = [
            ['name' => 'CharOne', 'is_online' => true],
            ['name' => 'CharTwo', 'is_online' => true],
            ['name' => 'CharThree', 'is_online' => false],
        ];
        $event = new OnlineCharactersUpdated(collect($characters), 'TestGuild');

        $data = $event->broadcastWith();

        $this->assertCount(3, $data['onlineCharacters']);
    }
}
