<?php

namespace Tests\Feature\App\Console\Commands;

use App\Character\GuildEnum;
use App\Character\GuildPageCharacter;
use App\Character\VocationEnum;
use App\Console\Commands\WorldScraper;
use App\Events\OnlineCharactersUpdated;
use App\Models\Character;
use App\Models\Setting;
use App\Setting\SettingConfig;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Tests\Support\GuildPageHtml;
use Tests\TestCase;

class WorldScraperStub extends WorldScraper {
    private string $fakeHtml = '';

    public function setFakeHtml(string $html): void {
        $this->fakeHtml = $html;
    }

    protected function dispatchRequest(string $url): string {
        return $this->fakeHtml;
    }
}

class WorldScraperTest extends TestCase {


    protected function setUp(): void {
        parent::setUp();
        Storage::fake('local');
        Cache::flush();
        \Illuminate\Support\Facades\DB::table('settings')->truncate();
        \Illuminate\Support\Facades\DB::table('characters')->truncate();
    }

    private function runStub(WorldScraperStub $stub): void {
        $stub->setLaravel($this->app);
        $stub->run(new ArrayInput([]), new NullOutput());
    }

    private function makeOnlineCharacter(string $guildName): GuildPageCharacter {
        $character = new GuildPageCharacter();
        $character->rank = 'Leader';
        $character->name = 'TestCharacter';
        $character->vocation = VocationEnum::EK;
        $character->level = 100;
        $character->joining_date = Carbon::now();
        $character->is_online = true;
        $character->guild_name = $guildName;
        return $character;
    }

    private function makeOfflineCharacter(string $guildName): GuildPageCharacter {
        $character = new GuildPageCharacter();
        $character->rank = 'Leader';
        $character->name = 'OfflineCharacter';
        $character->vocation = VocationEnum::EK;
        $character->level = 200;
        $character->joining_date = Carbon::now();
        $character->is_online = false;
        $character->guild_name = $guildName;
        return $character;
    }

    public function testHandle_WhenOnlineCharacterExists_ShouldDispatchOnlineCharactersUpdatedEventWithCharacter(): void {
        Event::fake();
        $guildName = GuildEnum::OUTLAW->value;
        Setting::factory()->create([
            'name' => SettingConfig::GUILD_NAME->value,
            'value' => $guildName,
        ]);
        $onlineCharacter = $this->makeOnlineCharacter($guildName);
        Character::factory()->create([
            'name' => $onlineCharacter->name,
            'guild_name' => $guildName,
            'is_online' => false,
        ]);

        $stub = $this->app->make(WorldScraperStub::class);
        $stub->setFakeHtml(GuildPageHtml::listOfCharacters($onlineCharacter));
        $this->runStub($stub);

        Event::assertDispatched(OnlineCharactersUpdated::class, function (OnlineCharactersUpdated $event) {
            return count($event->changes) === 1
                && $event->changes[0]['name'] === 'TestCharacter';
        });
    }

    public function testHandle_ShouldDispatchEventWithCorrectGuildName(): void {
        Event::fake();
        $guildName = GuildEnum::OUTLAW->value;
        Setting::factory()->create([
            'name' => SettingConfig::GUILD_NAME->value,
            'value' => $guildName,
        ]);
        $onlineCharacter = $this->makeOnlineCharacter($guildName);
        Character::factory()->create([
            'name' => $onlineCharacter->name,
            'guild_name' => $guildName,
            'is_online' => false,
        ]);

        $stub = $this->app->make(WorldScraperStub::class);
        $stub->setFakeHtml(GuildPageHtml::listOfCharacters($onlineCharacter));
        $this->runStub($stub);

        Event::assertDispatched(OnlineCharactersUpdated::class, function (OnlineCharactersUpdated $event) use ($guildName) {
            return $event->guildName === $guildName;
        });
    }

    public function testHandle_WhenNoOnlineCharacters_ShouldDispatchEventWithRecentlyOfflineCharacters(): void {
        Event::fake();
        $guildName = GuildEnum::OUTLAW->value;
        Setting::factory()->create([
            'name' => SettingConfig::GUILD_NAME->value,
            'value' => $guildName,
        ]);
        $offlineCharacter = $this->makeOfflineCharacter($guildName);
        Character::factory()->create([
            'name' => $offlineCharacter->name,
            'guild_name' => $guildName,
            'is_online' => true,
            'online_at' => Carbon::now()->subMinutes(5),
        ]);

        $stub = $this->app->make(WorldScraperStub::class);
        $stub->setFakeHtml(GuildPageHtml::listOfCharacters($offlineCharacter));
        $this->runStub($stub);

        Event::assertDispatched(OnlineCharactersUpdated::class, function (OnlineCharactersUpdated $event) {
            return count($event->changes) === 1
                && $event->changes[0]['is_online'] === false;
        });
    }
}
