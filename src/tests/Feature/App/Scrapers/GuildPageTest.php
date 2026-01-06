<?php

namespace Tests\Feature\App\Scrapers;
use App\Character\GuildEnum;
use App\Character\GuildPageCharacter;
use App\Character\VocationEnum;
use App\Models\Character;
use App\Scrapers\GuildPage;
use Carbon\Carbon;
use Tests\Support\GuildPageHtml;
use Tests\TestCase;

class GuildPageTest extends TestCase {

    public function testScrap_WhenPlayerIsOfflineInDatabase_AndIsOnlineOnHtml_ShouldMarkAsOnline(): void {
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

        $guildPage = GuildPage::getInstance($html, GuildEnum::OUTLAW->value);
        $guildPage->scrap();

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

        $guildPage = GuildPage::getInstance($html, GuildEnum::OUTLAW->value);
        $guildPage->scrap();

        $databaseCharacter->refresh();
        $this->assertFalse($databaseCharacter->is_online);
        $this->assertNull($databaseCharacter->online_at);
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

        $guildPage = GuildPage::getInstance($html, GuildEnum::OUTLAW->value);
        $guildPage->scrap();

        $foundCharacter = Character::where('id', $databaseCharacter->id)->first();
        $this->assertNull($foundCharacter);
    }
}
