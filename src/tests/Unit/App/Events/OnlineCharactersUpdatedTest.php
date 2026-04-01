<?php

namespace Tests\Unit\App\Events;

use App\Events\OnlineCharactersUpdated;
use Illuminate\Broadcasting\Channel;
use PHPUnit\Framework\TestCase;

class OnlineCharactersUpdatedTest extends TestCase {

    public function testBroadcastOn_ShouldReturnPublicChannelWithFixedName(): void {
        $event = new OnlineCharactersUpdated([], [], 'TestGuild');

        $channel = $event->broadcastOn();

        $this->assertInstanceOf(Channel::class, $channel);
        $this->assertEquals('online-characters', $channel->name);
    }

    public function testBroadcastWith_ShouldReturnChangesAndRemovedKeys(): void {
        $character = ['name' => 'TestChar', 'is_online' => true, 'vocation' => 'Knight', 'level' => 100];
        $event = new OnlineCharactersUpdated([$character], [], 'TestGuild');

        $data = $event->broadcastWith();

        $this->assertArrayHasKey('changes', $data);
        $this->assertArrayHasKey('removed', $data);
        $this->assertCount(1, $data['changes']);
        $this->assertEquals('TestChar', $data['changes'][0]['name']);
    }

    public function testBroadcastWith_WhenNoChanges_ShouldReturnEmptyArrays(): void {
        $event = new OnlineCharactersUpdated([], [], 'TestGuild');

        $data = $event->broadcastWith();

        $this->assertEmpty($data['changes']);
        $this->assertEmpty($data['removed']);
    }

    public function testBroadcastWith_ShouldIncludeAllCharacterFields(): void {
        $character = [
            'name' => 'TestChar',
            'is_online' => true,
            'vocation' => 'Knight',
            'level' => 100,
        ];
        $event = new OnlineCharactersUpdated([$character], [], 'TestGuild');

        $data = $event->broadcastWith();

        $charData = $data['changes'][0];
        $this->assertEquals('TestChar', $charData['name']);
        $this->assertEquals('Knight', $charData['vocation']);
        $this->assertEquals(100, $charData['level']);
        $this->assertTrue($charData['is_online']);
    }

    public function testBroadcastWith_ShouldPreserveMultipleChanges(): void {
        $characters = [
            ['name' => 'CharOne', 'is_online' => true],
            ['name' => 'CharTwo', 'is_online' => true],
            ['name' => 'CharThree', 'is_online' => false],
        ];
        $event = new OnlineCharactersUpdated($characters, [], 'TestGuild');

        $data = $event->broadcastWith();

        $this->assertCount(3, $data['changes']);
    }

    public function testBroadcastWith_ShouldIncludeRemovedNames(): void {
        $event = new OnlineCharactersUpdated([], ['CharX', 'CharY'], 'TestGuild');

        $data = $event->broadcastWith();

        $this->assertEmpty($data['changes']);
        $this->assertCount(2, $data['removed']);
        $this->assertContains('CharX', $data['removed']);
        $this->assertContains('CharY', $data['removed']);
    }
}
