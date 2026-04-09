<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Models\Character;
use App\Models\ExecutionCrawler;
use App\Models\Setting;
use App\Models\User;
use App\Setting\SettingConfig;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomeControllerTest extends TestCase {

    protected function setUp(): void {
        parent::setUp();
        Storage::fake('local');
    }

    public function testIndex_ShouldReturnObserverView(): void {
        Setting::factory()->create(['name' => SettingConfig::GUILD_NAME->value, 'value' => 'TestGuild']);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewIs('observer');
    }

    public function testGetOnlineCharacters_WhenUnauthenticated_ShouldReturnOnlyOnlineCharacters(): void {
        Character::factory()->create(['is_online' => true, 'offline_at' => null]);
        Character::factory()->create(['is_online' => false, 'offline_at' => now()->subMinutes(5)]);

        $response = $this->getJson(route('get-online-characters'));

        $response->assertStatus(200);
        $onlineCharacters = $response->json('onlineCharacters');
        $this->assertCount(1, $onlineCharacters);
        $this->assertTrue($onlineCharacters[0]['is_online']);
    }

    public function testIndex_WhenUnauthenticated_ShouldNotIncludeOfflineScript(): void {
        Setting::factory()->create(['name' => SettingConfig::GUILD_NAME->value, 'value' => 'TestGuild']);

        $response = $this->get(route('home'));

        $response->assertDontSee('observe-offline.js', false);
    }

    public function testIndex_WhenAuthenticated_ShouldIncludeOfflineScript(): void {
        Setting::factory()->create(['name' => SettingConfig::GUILD_NAME->value, 'value' => 'TestGuild']);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.home'));

        $response->assertSee('observe-offline.js', false);
    }

    public function testIndex_WhenAuthenticated_ShouldLoadObserveJsBeforeOfflineJs(): void {
        Setting::factory()->create(['name' => SettingConfig::GUILD_NAME->value, 'value' => 'TestGuild']);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.home'));

        $content = $response->getContent();
        $observeJsPos = strpos($content, 'observe.js');
        $offlineJsPos = strpos($content, 'observe-offline.js');
        $this->assertNotFalse($observeJsPos);
        $this->assertNotFalse($offlineJsPos);
        $this->assertLessThan($offlineJsPos, $observeJsPos, 'observe.js deve ser carregado antes de observe-offline.js');
    }

    public function testIndex_WhenAuthenticated_ShouldIncludeViteAppScript(): void {
        Setting::factory()->create(['name' => SettingConfig::GUILD_NAME->value, 'value' => 'TestGuild']);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.home'));

        $response->assertSee('<script type="module"', false);
    }

    public function testGetOnlineCharacters_WhenAuthenticated_ShouldReturnOnlineAndRecentlyOfflineCharacters(): void {
        $user = User::factory()->create();
        Character::factory()->create(['is_online' => true, 'offline_at' => null]);
        Character::factory()->create(['is_online' => false, 'offline_at' => now()->subMinutes(5)]);

        $response = $this->actingAs($user)->getJson(route('get-online-characters'));

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('onlineCharacters'));
    }

    public function testSetCharacterType_WhenUnauthenticated_ShouldRedirectToLogin(): void {
        $response = $this->post(route('update.character.type', ['characterName' => 'Test', 'type' => 'enemy']));

        $response->assertRedirect(route('login.index'));
    }

    public function testSetCharacterType_WhenAuthenticated_ShouldUpdateCharacterType(): void {
        $user = User::factory()->create();
        $character = Character::factory()->create(['name' => 'TestChar', 'type' => 'enemy']);

        $response = $this->actingAs($user)->post(
            route('update.character.type', ['characterName' => 'TestChar', 'type' => 'ally'])
        );

        $response->assertStatus(200);
        $this->assertEquals('ally', $character->fresh()->type);
    }

    public function testSetCharacterAsAttacker_WhenUnauthenticated_ShouldRedirectToLogin(): void {
        $response = $this->post(
            route('update.character.attacker', ['characterName' => 'Test', 'isAttacker' => 'true'])
        );

        $response->assertRedirect(route('login.index'));
    }

    public function testSetCharacterAsAttacker_WhenAuthenticated_ShouldMarkCharacterAsAttacker(): void {
        $user = User::factory()->create();
        $character = Character::factory()->create(['name' => 'TestChar', 'is_attacker_character' => false]);

        $this->actingAs($user)->post(
            route('update.character.attacker', ['characterName' => 'TestChar', 'isAttacker' => 'true'])
        );

        $this->assertTrue($character->fresh()->is_attacker_character);
    }

    public function testSetCharacterAsAttacker_WhenPassingFalse_ShouldUnmarkCharacterAsAttacker(): void {
        $user = User::factory()->create();
        $character = Character::factory()->create(['name' => 'TestChar', 'is_attacker_character' => true]);

        $this->actingAs($user)->post(
            route('update.character.attacker', ['characterName' => 'TestChar', 'isAttacker' => 'false'])
        );

        $this->assertFalse($character->fresh()->is_attacker_character);
    }

    public function testSettings_WhenUnauthenticated_ShouldRedirectToLogin(): void {
        $response = $this->get(route('settings'));

        $response->assertRedirect(route('login.index'));
    }

    public function testSettings_WhenAuthenticated_ShouldReturnSettingsView(): void {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('settings'));

        $response->assertStatus(200);
        $response->assertViewIs('settings');
    }

    public function testSaveSettings_WhenAuthenticated_ShouldPersistGuildName(): void {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('settings.save'), ['guild_name' => 'NewGuild']);

        $this->assertDatabaseHas('settings', [
            'name' => SettingConfig::GUILD_NAME->value,
            'value' => 'NewGuild',
        ]);
    }

    public function testRefil_ShouldReturnRefilView(): void {
        $response = $this->get(route('refil'));

        $response->assertStatus(200);
        $response->assertViewIs('refil');
    }

    public function testHealthcheck_WhenLastExecutionWasLessThanOneMinuteAgo_ShouldReturn200(): void {
        ExecutionCrawler::factory()->recentExecution()->create();

        $response = $this->getJson(route('healthcheck'));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);
    }

    public function testHealthcheck_WhenLastExecutionWasMoreThanOneMinuteAgo_ShouldReturn500(): void {
        ExecutionCrawler::factory()->outdatedExecution()->create();

        $response = $this->getJson(route('healthcheck'));

        $response->assertStatus(500);
        $response->assertJson(['status' => 'error']);
    }

    public function testHealthcheck_WhenNoCrawlerExecutionExists_ShouldReturn500(): void {
        $response = $this->getJson(route('healthcheck'));

        $response->assertStatus(500);
        $response->assertJson(['status' => 'error']);
    }
}
