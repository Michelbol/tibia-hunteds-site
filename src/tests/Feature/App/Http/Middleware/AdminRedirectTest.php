<?php

namespace Tests\Feature\App\Http\Middleware;

use App\Models\Setting;
use App\Models\User;
use App\Setting\SettingConfig;
use Tests\TestCase;

class AdminRedirectTest extends TestCase
{
    public function testHandle_WhenAuthenticatedAndAccessingHome_ShouldRedirectToAdminHome(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertRedirect(route('admin.home'));
    }

    public function testHandle_WhenUnauthenticatedAndAccessingHome_ShouldNotRedirect(): void
    {
        Setting::factory()->create(['name' => SettingConfig::GUILD_NAME->value, 'value' => 'TestGuild']);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
    }

    public function testHandle_WhenAuthenticatedAndAccessingOtherRoute_ShouldNotRedirect(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('refil'));

        $response->assertStatus(200);
    }
}
