<?php

namespace Tests\Feature\App\Scrapers;
use App\Character\GuildEnum;
use App\Character\GuildPageCharacter;
use App\Character\VocationEnum;
use App\Models\Character;
use App\Models\Setting;
use App\Scrapers\GuildPage;
use App\Setting\SettingConfig;
use Carbon\Carbon;
use Tests\Support\GuildPageHtml;
use Tests\TestCase;

class GuildPageTest extends TestCase {

    public function testScrap_WhenPlayerIsOfflineInDatabase_AndIsOnlineOnHtml_ShouldMarkAsOnline(): void {
        Setting::factory()->create(['name' => SettingConfig::GUILD_NAME->value]);
        Carbon::setTestNow($expectedOnlineAt = Carbon::now());
        $guildPageCharacter = new GuildPageCharacter();
        $guildPageCharacter->rank = 'Leader';
        $guildPageCharacter->name = 'Fabio Selokoo';
        $guildPageCharacter->vocation = VocationEnum::EK;
        $guildPageCharacter->level = '1264';
        $guildPageCharacter->joining_date = Carbon::now();
        $guildPageCharacter->is_online = true;
        $databaseCharacter = Character::factory()->create([
            'name' => $guildPageCharacter->name,
            'vocation' => $guildPageCharacter->vocation,
            'level' => $guildPageCharacter->level,
            'joining_date' => $guildPageCharacter->joining_date,
            'is_online' => false,
            'online_at' => null,
            'guild_name' => GuildEnum::OUTLAW->value,
        ]);

        $html = GuildPageHtml::listOfCharacters($guildPageCharacter);

        GuildPage::getInstance($html, GuildEnum::OUTLAW->value)->scrap();

        $databaseCharacter->refresh();
        $this->assertTrue($databaseCharacter->is_online);
        $this->assertEquals($expectedOnlineAt->toDateTimeString(), $databaseCharacter->online_at->toDateTimeString());
    }

    public function testScrap_WhenPlayerIsOnlineInDatabase_AndIsOfflineOnHtml_ShouldMarkAsOffline() {
        Carbon::setTestNow($expectedOnlineAt = Carbon::now());
        $guildPageCharacter = new GuildPageCharacter();
        $guildPageCharacter->rank = 'Leader';
        $guildPageCharacter->name = 'Fabio Selokoo';
        $guildPageCharacter->vocation = VocationEnum::EK;
        $guildPageCharacter->level = '1264';
        $guildPageCharacter->joining_date = Carbon::now();
        $guildPageCharacter->is_online = false;

        $databaseCharacter = Character::factory()->create([
            'name' => $guildPageCharacter->name,
            'vocation' => $guildPageCharacter->vocation,
            'level' => $guildPageCharacter->level,
            'joining_date' => $guildPageCharacter->joining_date,
            'is_online' => true,
            'online_at' => now()->subHours(2),
            'guild_name' => GuildEnum::OUTLAW->value,
        ]);

        $html = GuildPageHtml::listOfCharacters($guildPageCharacter);

        GuildPage::getInstance($html, GuildEnum::OUTLAW->value)->scrap();

        $databaseCharacter->refresh();
        $this->assertFalse($databaseCharacter->is_online);
        $this->assertNull($databaseCharacter->online_at);
    }

    public function testScrap_WhenTrHasLessThan6Tds_ShouldSkipRowWithoutError(): void {
        $html = GuildPageHtml::listOfCharactersWithInvalidTdCount();

        GuildPage::getInstance($html, GuildEnum::OUTLAW->value)->scrap();

        $this->assertDatabaseCount('characters', 0);
    }

    public function testScrap_WhenTrHasLabelHClass_ShouldSkipRow(): void {
        $html = GuildPageHtml::onlyLabelHRow();

        GuildPage::getInstance($html, GuildEnum::OUTLAW->value)->scrap();

        $this->assertDatabaseCount('characters', 0);
    }

    public function testScrap_WhenTrHasDoNotBreakClass_ShouldSkipInvitationBoardRow(): void {
        $html = GuildPageHtml::withDoNotBreakRow();

        GuildPage::getInstance($html, GuildEnum::OUTLAW->value)->scrap();

        $this->assertDatabaseCount('characters', 0);
    }

    public function testScrap_WhenTrContainsNoInvitedCharactersText_ShouldSkipRow(): void {
        $html = GuildPageHtml::withNoInvitedCharactersText();

        GuildPage::getInstance($html, GuildEnum::OUTLAW->value)->scrap();

        $this->assertDatabaseCount('characters', 0);
    }

    public function testScrap_WhenHtmlHasNoRows_ShouldReturnEarlyWithoutUpdatingDatabase(): void {
        $databaseCharacter = Character::factory()->create([
            'is_online' => true,
            'guild_name' => GuildEnum::OUTLAW->value,
        ]);

        GuildPage::getInstance(GuildPageHtml::emptyTable(), GuildEnum::OUTLAW->value)->scrap();

        $databaseCharacter->refresh();
        $this->assertTrue($databaseCharacter->is_online);
    }

    public function testScrap_WhenPlayerIsOnlineInHtml_AndDoesNotExistInDatabase_ShouldCreateCharacterAndMarkAsOnline(): void {
        Carbon::setTestNow(Carbon::now());
        $guildPageCharacter = new GuildPageCharacter();
        $guildPageCharacter->rank = 'Leader';
        $guildPageCharacter->name = 'New Character';
        $guildPageCharacter->vocation = VocationEnum::EK;
        $guildPageCharacter->level = '500';
        $guildPageCharacter->joining_date = Carbon::now();
        $guildPageCharacter->is_online = true;

        $html = GuildPageHtml::listOfCharacters($guildPageCharacter);

        GuildPage::getInstance($html, GuildEnum::OUTLAW->value)->scrap();

        $this->assertDatabaseCount('characters', 1);
        $created = Character::where('name', 'New Character')->first();
        $this->assertTrue($created->is_online);
    }

    public function testScrap_WhenPlayerIsOnlineInHtml_AndExistsInDatabaseWithDifferentGuild_ShouldReuseExistingCharacterAndMarkAsOnline(): void {
        Carbon::setTestNow(Carbon::now());
        $guildPageCharacter = new GuildPageCharacter();
        $guildPageCharacter->rank = 'Leader';
        $guildPageCharacter->name = 'Fabio Selokoo';
        $guildPageCharacter->vocation = VocationEnum::EK;
        $guildPageCharacter->level = '1264';
        $guildPageCharacter->joining_date = Carbon::now();
        $guildPageCharacter->is_online = true;
        Character::factory()->create([
            'name' => $guildPageCharacter->name,
            'vocation' => $guildPageCharacter->vocation,
            'level' => $guildPageCharacter->level,
            'joining_date' => $guildPageCharacter->joining_date,
            'is_online' => false,
            'guild_name' => GuildEnum::QUELIBRALAND->value,
        ]);

        $html = GuildPageHtml::listOfCharacters($guildPageCharacter);

        GuildPage::getInstance($html, GuildEnum::OUTLAW->value)->scrap();

        $this->assertDatabaseCount('characters', 1);
        $character = Character::where('name', $guildPageCharacter->name)->first();
        $this->assertTrue($character->is_online);
    }

    public function testScrap_WhenPlayerIsAlreadyOnlineInDatabase_AndIsOnlineOnHtml_ShouldKeepOriginalOnlineAt(): void {
        $originalOnlineAt = Carbon::now()->subHour();
        $guildPageCharacter = new GuildPageCharacter();
        $guildPageCharacter->rank = 'Leader';
        $guildPageCharacter->name = 'Fabio Selokoo';
        $guildPageCharacter->vocation = VocationEnum::EK;
        $guildPageCharacter->level = '1264';
        $guildPageCharacter->joining_date = Carbon::now();
        $guildPageCharacter->is_online = true;
        $databaseCharacter = Character::factory()->create([
            'name' => $guildPageCharacter->name,
            'vocation' => $guildPageCharacter->vocation,
            'level' => $guildPageCharacter->level,
            'joining_date' => $guildPageCharacter->joining_date,
            'is_online' => true,
            'online_at' => $originalOnlineAt,
            'guild_name' => GuildEnum::OUTLAW->value,
        ]);

        $html = GuildPageHtml::listOfCharacters($guildPageCharacter);

        GuildPage::getInstance($html, GuildEnum::OUTLAW->value)->scrap();

        $databaseCharacter->refresh();
        $this->assertEquals($originalOnlineAt->toDateTimeString(), $databaseCharacter->online_at->toDateTimeString());
    }

    public function testScrap_WhenPlayerIsOfflineInDatabase_AndDoesNotExistInHtml_ShouldDeleteFromDatabase(): void {
        Character::factory()->create([
            'name' => 'Ghost Player',
            'guild_name' => GuildEnum::OUTLAW->value,
            'is_online' => false,
        ]);
        $guildPageCharacter = new GuildPageCharacter();
        $guildPageCharacter->rank = 'Leader';
        $guildPageCharacter->name = 'Fabio Selokoo';
        $guildPageCharacter->vocation = VocationEnum::EK;
        $guildPageCharacter->level = '1264';
        $guildPageCharacter->joining_date = Carbon::now();
        $guildPageCharacter->is_online = false;
        Character::factory()->create([
            'name' => $guildPageCharacter->name,
            'guild_name' => GuildEnum::OUTLAW->value,
            'is_online' => false,
        ]);

        $html = GuildPageHtml::listOfCharacters($guildPageCharacter);

        GuildPage::getInstance($html, GuildEnum::OUTLAW->value)->scrap();

        $this->assertNull(Character::where('name', 'Ghost Player')->first());
    }

    public function testScrap_WithMultipleCharacters_ShouldCorrectlyMarkOnlineAndOffline(): void {
        Carbon::setTestNow(Carbon::now());
        $onlineChar = new GuildPageCharacter();
        $onlineChar->rank = 'Leader';
        $onlineChar->name = 'Online Player';
        $onlineChar->vocation = VocationEnum::EK;
        $onlineChar->level = '500';
        $onlineChar->joining_date = Carbon::now();
        $onlineChar->is_online = true;

        $offlineChar = new GuildPageCharacter();
        $offlineChar->rank = 'Member';
        $offlineChar->name = 'Offline Player';
        $offlineChar->vocation = VocationEnum::MS;
        $offlineChar->level = '300';
        $offlineChar->joining_date = Carbon::now();
        $offlineChar->is_online = false;

        Character::factory()->create(['name' => $onlineChar->name, 'guild_name' => GuildEnum::OUTLAW->value, 'is_online' => false]);
        $offlineDbChar = Character::factory()->create(['name' => $offlineChar->name, 'guild_name' => GuildEnum::OUTLAW->value, 'is_online' => true]);

        $html = GuildPageHtml::listOfMultipleCharacters([$onlineChar, $offlineChar]);

        GuildPage::getInstance($html, GuildEnum::OUTLAW->value)->scrap();

        $this->assertTrue(Character::where('name', $onlineChar->name)->first()->is_online);
        $offlineDbChar->refresh();
        $this->assertFalse($offlineDbChar->is_online);
    }

    public function testGetOnlineCharacters_AfterScrap_ShouldReturnCorrectCount(): void {
        Carbon::setTestNow(Carbon::now());
        $char1 = new GuildPageCharacter();
        $char1->rank = 'Leader';
        $char1->name = 'Player One';
        $char1->vocation = VocationEnum::EK;
        $char1->level = '500';
        $char1->joining_date = Carbon::now();
        $char1->is_online = true;

        $char2 = new GuildPageCharacter();
        $char2->rank = 'Member';
        $char2->name = 'Player Two';
        $char2->vocation = VocationEnum::MS;
        $char2->level = '300';
        $char2->joining_date = Carbon::now();
        $char2->is_online = true;

        Character::factory()->create(['name' => $char1->name, 'guild_name' => GuildEnum::OUTLAW->value, 'is_online' => false]);
        Character::factory()->create(['name' => $char2->name, 'guild_name' => GuildEnum::OUTLAW->value, 'is_online' => false]);

        $html = GuildPageHtml::listOfMultipleCharacters([$char1, $char2]);
        $guildPage = GuildPage::getInstance($html, GuildEnum::OUTLAW->value)->scrap();

        $this->assertEquals(2, $guildPage->getOnlineCharacters());
    }

    public function testGetOfflineCharacters_AfterScrap_ShouldReturnCorrectCount(): void {
        Carbon::setTestNow(Carbon::now());
        $onlineChar = new GuildPageCharacter();
        $onlineChar->rank = 'Leader';
        $onlineChar->name = 'Online One';
        $onlineChar->vocation = VocationEnum::EK;
        $onlineChar->level = '500';
        $onlineChar->joining_date = Carbon::now();
        $onlineChar->is_online = true;

        $offlineChar = new GuildPageCharacter();
        $offlineChar->rank = 'Member';
        $offlineChar->name = 'Offline One';
        $offlineChar->vocation = VocationEnum::MS;
        $offlineChar->level = '300';
        $offlineChar->joining_date = Carbon::now();
        $offlineChar->is_online = false;

        Character::factory()->create(['name' => $onlineChar->name, 'guild_name' => GuildEnum::OUTLAW->value, 'is_online' => false]);
        Character::factory()->create(['name' => $offlineChar->name, 'guild_name' => GuildEnum::OUTLAW->value, 'is_online' => false]);

        $html = GuildPageHtml::listOfMultipleCharacters([$onlineChar, $offlineChar]);
        $guildPage = GuildPage::getInstance($html, GuildEnum::OUTLAW->value)->scrap();

        $this->assertEquals(1, $guildPage->getOfflineCharacters());
    }

    public function testScrap_WhenPlayerIsOnlineInDatabase_AndDidntExistsInHtml_ShouldMarkAsOfflineAndDeleteFromDatabase() {
        Carbon::setTestNow($expectedOnlineAt = Carbon::now());
        $guildPageCharacter = new GuildPageCharacter();
        $guildPageCharacter->rank = 'Leader';
        $guildPageCharacter->name = 'Fabio Selokoo';
        $guildPageCharacter->vocation = VocationEnum::EK;
        $guildPageCharacter->level = '1264';
        $guildPageCharacter->joining_date = Carbon::now();
        $guildPageCharacter->is_online = false;

        $databaseCharacter = Character::factory()->create([
            'name' => 'Coruja MegaStar',
            'vocation' => VocationEnum::MS,
            'level' => '200',
            'joining_date' => Carbon::now(),
            'is_online' => true,
            'online_at' => now()->subHours(2),
            'guild_name' => GuildEnum::OUTLAW->value,
        ]);

        $html = GuildPageHtml::listOfCharacters($guildPageCharacter);

        GuildPage::getInstance($html, GuildEnum::OUTLAW->value)->scrap();

        $foundCharacter = Character::where('id', $databaseCharacter->id)->first();
        $this->assertNull($foundCharacter);
    }
}
