<?php

namespace Tests\Feature\App\Character\Repository;

use App\Character\Repository\DatabaseCharacterRepository;
use App\Models\Character;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DatabaseCharacterRepositoryTest extends TestCase {

    private DatabaseCharacterRepository $repository;

    protected function setUp(): void {
        parent::setUp();
        Storage::fake('local');
        $this->repository = new DatabaseCharacterRepository();
    }

    public function testGetOnlinePlayer_WhenCharacterIsOnline_ShouldReturnIt(): void {
        $character = Character::factory()->create(['is_online' => true, 'offline_at' => null]);

        $result = $this->repository->getOnlinePlayer();

        $this->assertTrue($result->contains('id', $character->id));
    }

    public function testGetOnlinePlayer_WhenCharacterIsOfflineWithin15Minutes_ShouldReturnIt(): void {
        $character = Character::factory()->create([
            'is_online' => false,
            'offline_at' => now()->subMinutes(10),
        ]);

        $result = $this->repository->getOnlinePlayer();

        $this->assertTrue($result->contains('id', $character->id));
    }

    public function testGetOnlinePlayer_WhenCharacterIsOfflineMoreThan15Minutes_ShouldNotReturnIt(): void {
        $character = Character::factory()->create([
            'is_online' => false,
            'offline_at' => now()->subMinutes(16),
        ]);

        $result = $this->repository->getOnlinePlayer();

        $this->assertFalse($result->contains('id', $character->id));
    }

    public function testGetOnlinePlayer_WhenGuildNameProvided_ShouldFilterByGuild(): void {
        $characterA = Character::factory()->create(['is_online' => true, 'guild_name' => 'GuildA']);
        $characterB = Character::factory()->create(['is_online' => true, 'guild_name' => 'GuildB']);

        $result = $this->repository->getOnlinePlayer('GuildA');

        $this->assertTrue($result->contains('id', $characterA->id));
        $this->assertFalse($result->contains('id', $characterB->id));
    }

    public function testGetOnlinePlayer_ShouldSaveResultToStorage(): void {
        Character::factory()->create(['is_online' => true]);

        $this->repository->getOnlinePlayer();

        $files = Storage::disk('local')->files('/cache/online-characters');
        $this->assertCount(1, $files);
    }

    public function testFirstCharacterByName_WhenCharacterExists_ShouldReturnIt(): void {
        $character = Character::factory()->create(['name' => 'Fabio Selokoo']);

        $result = $this->repository->firstCharacterByName('Fabio Selokoo');

        $this->assertNotNull($result);
        $this->assertEquals($character->id, $result->id);
    }

    public function testFirstCharacterByName_WhenCharacterDoesNotExist_ShouldReturnNull(): void {
        $result = $this->repository->firstCharacterByName('Unknown Character');

        $this->assertNull($result);
    }

    public function testUpdateCharacterTypeUsingName_ShouldUpdateType(): void {
        $character = Character::factory()->create(['name' => 'Fabio Selokoo', 'type' => 'enemy']);

        $this->repository->updateCharacterTypeUsingName('Fabio Selokoo', 'ally');

        $this->assertEquals('ally', $character->fresh()->type);
    }

    public function testUpsertCharacters_WhenCharacterDoesNotExist_ShouldCreateIt(): void {
        $data = collect([[
            'name' => 'New Character',
            'vocation' => 'Knight',
            'level' => 100,
            'joining_date' => '2024-01-01',
            'is_online' => true,
            'guild_name' => 'TestGuild',
            'online_at' => null,
            'offline_at' => null,
        ]]);

        $this->repository->upsertCharacters($data);

        $this->assertDatabaseHas('characters', ['name' => 'New Character', 'level' => 100]);
    }

    public function testUpsertCharacters_WhenCharacterExists_ShouldUpdateLevel(): void {
        Character::factory()->create(['name' => 'Existing Character', 'level' => 100, 'guild_name' => 'TestGuild']);

        $data = collect([[
            'name' => 'Existing Character',
            'vocation' => 'Knight',
            'level' => 200,
            'joining_date' => '2024-01-01',
            'is_online' => true,
            'guild_name' => 'TestGuild',
            'online_at' => null,
            'offline_at' => null,
        ]]);

        $this->repository->upsertCharacters($data);

        $this->assertDatabaseHas('characters', ['name' => 'Existing Character', 'level' => 200]);
    }

    public function testGetAllCharactersByGuildName_ShouldReturnOnlyGuildCharacters(): void {
        Character::factory()->create(['guild_name' => 'TargetGuild']);
        Character::factory()->create(['guild_name' => 'OtherGuild']);

        $result = $this->repository->getAllCharactersByGuildName('TargetGuild');

        $this->assertCount(1, $result);
        $this->assertEquals('TargetGuild', $result->first()->guild_name);
    }

    public function testUpdateCharacterIsAttacker_ShouldUpdateFlag(): void {
        $character = Character::factory()->create(['name' => 'Attacker', 'is_attacker_character' => false]);

        $this->repository->updateCharacterIsAttacker('Attacker', true);

        $this->assertTrue($character->fresh()->is_attacker_character);
    }
}
