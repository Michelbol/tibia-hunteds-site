<?php

namespace Tests\Unit\App\Character;

use App\Character\GuildPageCharacter;
use App\Character\VocationEnum;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class GuildPageCharacterTest extends TestCase {

    public function testRemoveSpace_WhenStringHasNbsp_ShouldReplaceWithSpace(): void {
        $result = GuildPageCharacter::removeSpace("Hello\xC2\xA0World");

        $this->assertEquals('Hello World', $result);
    }

    public function testRemoveSpace_WhenStringHasMultipleSpaces_ShouldNormalizeToOne(): void {
        $result = GuildPageCharacter::removeSpace('Hello   World');

        $this->assertEquals('Hello World', $result);
    }

    public function testRemoveSpace_WhenStringHasLeadingAndTrailingSpaces_ShouldTrim(): void {
        $result = GuildPageCharacter::removeSpace('  Hello World  ');

        $this->assertEquals('Hello World', $result);
    }

    public function testRemoveSpace_WhenStringHasHtmlNbspEntity_ShouldReplaceWithSpace(): void {
        $result = GuildPageCharacter::removeSpace('Hello&nbsp;World');

        $this->assertEquals('Hello World', $result);
    }

    public function testGetStatus_WhenStatusHasGreenSpanAndBoldTags_ShouldStripThem(): void {
        $html = '<span class="green"><b>online</b></span>';

        $result = GuildPageCharacter::getStatus($html);

        $this->assertEquals('online', $result);
    }

    public function testGetStatus_WhenStatusHasRedSpanTag_ShouldStripIt(): void {
        $html = '<span class="red">Offline</span>';

        $result = GuildPageCharacter::getStatus($html);

        $this->assertEquals('offline', $result);
    }

    public function testGetStatus_ShouldReturnLowercase(): void {
        $result = GuildPageCharacter::getStatus('ONLINE');

        $this->assertEquals('online', $result);
    }

    public function testToArray_WhenOnlineAtIsNull_ShouldNotIncludeOnlineAt(): void {
        $character = $this->buildCharacter();
        $character->online_at = null;

        $array = $character->toArray();

        $this->assertArrayNotHasKey('online_at', $array);
    }

    public function testToArray_WhenOnlineAtIsSet_ShouldIncludeOnlineAt(): void {
        $character = $this->buildCharacter();
        $character->online_at = Carbon::now();

        $array = $character->toArray();

        $this->assertArrayHasKey('online_at', $array);
    }

    public function testToArray_ShouldAlwaysIncludeOfflineAt(): void {
        $character = $this->buildCharacter();
        $character->offline_at = Carbon::now();

        $array = $character->toArray();

        $this->assertArrayHasKey('offline_at', $array);
    }

    public function testToArray_ShouldIncludeCoreFields(): void {
        $character = $this->buildCharacter();

        $array = $character->toArray();

        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('vocation', $array);
        $this->assertArrayHasKey('level', $array);
        $this->assertArrayHasKey('joining_date', $array);
        $this->assertArrayHasKey('is_online', $array);
        $this->assertArrayHasKey('guild_name', $array);
    }

    public function testGetJoiningDateFormated_ShouldReturnDateInExpectedFormat(): void {
        $character = new GuildPageCharacter();
        $character->joining_date = Carbon::create(2024, 3, 10);

        $result = $character->getJoiningDateFormated();

        $this->assertEquals('Mar 10 2024', $result);
    }

    private function buildCharacter(): GuildPageCharacter {
        $character = new GuildPageCharacter();
        $character->rank = 'Leader';
        $character->name = 'Test Character';
        $character->vocation = VocationEnum::EK;
        $character->level = 100;
        $character->joining_date = Carbon::now();
        $character->is_online = true;
        $character->guild_name = 'TestGuild';
        return $character;
    }
}
