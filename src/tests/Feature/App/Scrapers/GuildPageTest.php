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

        app(GuildPage::class)->scrap($html, GuildEnum::OUTLAW->value);

        $databaseCharacter->refresh();
        $this->assertTrue($databaseCharacter->is_online);
        $this->assertEquals($expectedOnlineAt->toDateTimeString(), $databaseCharacter->online_at->toDateTimeString());
    }
}
