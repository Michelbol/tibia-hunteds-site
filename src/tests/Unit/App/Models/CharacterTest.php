<?php

namespace Tests\Unit\App\Models;

use App\Models\Character;
use Carbon\Carbon;
use Tests\TestCase;

class CharacterTest extends TestCase {

    public function testToArray_ShouldIncludeAllExpectedFields(): void {
        $character = new Character();
        $character->name = 'Test';
        $character->vocation = 'Knight';
        $character->level = 100;
        $character->joining_date = '2024-01-01';
        $character->type = 'enemy';
        $character->is_online = true;
        $character->is_attacker_character = false;

        $array = $character->toArray();

        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('vocation', $array);
        $this->assertArrayHasKey('level', $array);
        $this->assertArrayHasKey('is_online', $array);
        $this->assertArrayHasKey('is_attacker_character', $array);
        $this->assertArrayHasKey('online_at', $array);
        $this->assertArrayHasKey('offline_at', $array);
        $this->assertArrayHasKey('position_time', $array);
        $this->assertArrayHasKey('position', $array);
    }

    public function testToArray_WhenDatesAreNull_ShouldSerializeAsNull(): void {
        $character = new Character();
        $character->online_at = null;
        $character->offline_at = null;
        $character->position_time = null;

        $array = $character->toArray();

        $this->assertNull($array['online_at']);
        $this->assertNull($array['offline_at']);
        $this->assertNull($array['position_time']);
    }

    public function testToArray_WhenDatesAreSet_ShouldFormatAsYmdHis(): void {
        $character = new Character();
        $character->online_at = Carbon::create(2024, 3, 10, 12, 0, 0);
        $character->offline_at = Carbon::create(2024, 3, 10, 13, 0, 0);
        $character->position_time = Carbon::create(2024, 3, 10, 14, 0, 0);

        $array = $character->toArray();

        $this->assertEquals('2024-03-10 12:00:00', $array['online_at']);
        $this->assertEquals('2024-03-10 13:00:00', $array['offline_at']);
        $this->assertEquals('2024-03-10 14:00:00', $array['position_time']);
    }

    public function testIsOnlineCast_WhenRetrievedFromDatabase_ShouldBeBoolean(): void {
        $character = Character::factory()->create(['is_online' => true]);

        $this->assertIsBool($character->fresh()->is_online);
        $this->assertTrue($character->fresh()->is_online);
    }

    public function testIsAttackerCharacterCast_WhenRetrievedFromDatabase_ShouldBeBoolean(): void {
        $character = Character::factory()->create(['is_attacker_character' => false]);

        $this->assertIsBool($character->fresh()->is_attacker_character);
        $this->assertFalse($character->fresh()->is_attacker_character);
    }

    public function testOnlineAtCast_WhenRetrievedFromDatabase_ShouldBeCarbonInstance(): void {
        $now = Carbon::now()->startOfSecond();
        $character = Character::factory()->create(['online_at' => $now]);

        $this->assertInstanceOf(Carbon::class, $character->fresh()->online_at);
        $this->assertEquals($now->toDateTimeString(), $character->fresh()->online_at->toDateTimeString());
    }
}
