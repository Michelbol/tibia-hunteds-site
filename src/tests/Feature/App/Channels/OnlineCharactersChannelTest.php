<?php

namespace Tests\Feature\App\Channels;

use Tests\TestCase;

class OnlineCharactersChannelTest extends TestCase {

    public function testOnlineCharactersChannel_ShouldAllowPublicAccess(): void {
        $response = $this->withoutMiddleware()->postJson('/broadcasting/auth', [
            'channel_name' => 'online-characters.TestGuild',
            'socket_id' => '1234.5678',
        ]);

        $response->assertStatus(200);
    }

    public function testOnlineCharactersChannel_WithDifferentGuildName_ShouldBeAccessible(): void {
        $response = $this->withoutMiddleware()->postJson('/broadcasting/auth', [
            'channel_name' => 'online-characters.AnotherGuild',
            'socket_id' => '1234.5678',
        ]);

        $response->assertStatus(200);
    }
}
