<?php

namespace App\Character;

use App\Character\Repository\CachedCharacterRepository;
use App\Character\Repository\CharacterRepository;
use App\Character\Repository\DatabaseCharacterRepository;
use App\Models\Character;
use Carbon\Carbon;
use Illuminate\Support\Collection;

readonly class CharacterService {
    private CharacterRepository $characterRepository;
    public function __construct(
        private DatabaseCharacterRepository $databaseCharacterRepository,
    ) {
        $this->characterRepository = new CachedCharacterRepository($this->databaseCharacterRepository);
    }

    public function retrieveOnlinePlayers(?string $guildName = null): Collection {
        return $this->characterRepository->getOnlinePlayer($guildName);
    }

    public function updateCharacterType(string $characterName, string $type): void {
        $this->databaseCharacterRepository->updateCharacterTypeUsingName($characterName, $type);
    }

    public function updateCharacterIsAttacker(string $characterName, bool $isAttacker): void {
        $this->databaseCharacterRepository->updateCharacterIsAttacker($characterName, $isAttacker);
    }

    public function createByGuildPageCharacter(GuildPageCharacter $guildPageCharacter): Character {
        $guildPageAdapter = new GuildPageCharacterAdapter();
        $character = $guildPageAdapter->convertGuildPageCharacterIntoCharacter($guildPageCharacter);
        $character->save();

        return $character;
    }

    public function update(Character $character, string $name, string $vocation, int $level, string $joiningDate, string $guildName): Character {
        $character->name = $name;
        $character->vocation = VocationEnum::from($vocation);
        $character->level = $level;
        $character->joining_date = Carbon::createFromFormat('M d Y', $joiningDate);
        $character->guild_name = urldecode($guildName);
        $character->save();

        return $character;
    }

    public function updateCharacterPosition(string $characterName, string $position): void {
        $character = $this->databaseCharacterRepository->firstCharacterByName($characterName);
        if (is_null($character)) {
            return;
        }
        $character->position = $position;
        $character->position_time = now();
        $character->save();
    }

    public function upsert(Collection $characters): void {
        if ($characters->isEmpty()) {
            return;
        }

        $this->databaseCharacterRepository->upsertCharacters($characters);
    }

    public function getAllCharactersByGuildName(?string $guildName): Collection {
        return $this->databaseCharacterRepository->getAllCharactersByGuildName($guildName);
    }
}
