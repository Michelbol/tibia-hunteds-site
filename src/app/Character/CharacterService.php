<?php

namespace App\Character;

use App\Character\Repository\CharacterRepository;
use App\Models\Character;
use Carbon\Carbon;
use Illuminate\Support\Collection;

readonly class CharacterService {
    public function __construct(
        private CharacterRepository $characterRepository,
    ) {}

    public function retrieveOnlinePlayers(?string $guildName = null): Collection {
        return $this->characterRepository->getOnlinePlayer($guildName);
    }

    public function updateCharacterType(string $characterName, string $type): void {
        $this->characterRepository->updateCharacterTypeUsingName($characterName, $type);
    }

    public function updateCharacterIsAttacker(string $characterName, bool $isAttacker): void {
        $this->characterRepository->updateCharacterIsAttacker($characterName, $isAttacker);
    }

    public function create(string $name, string $vocation, int $level, string $joiningDate, string $guildName): Character {
        $character = new Character();
        $character->name = $name;
        $character->vocation = VocationEnum::from($vocation);
        $character->level = $level;
        $character->joining_date = Carbon::createFromFormat('M d Y', $joiningDate);
        $character->is_online = false;
        $character->guild_name = $guildName;
        $character->online_at = null;
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
        $character = $this->characterRepository->firstCharacterByName($characterName);
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

        $this->characterRepository->upsertCharacters($characters);
    }

    public function getAllCharactersByGuildName(?string $guildName): Collection {
        return $this->characterRepository->getAllCharactersByGuildName($guildName);
    }
}
