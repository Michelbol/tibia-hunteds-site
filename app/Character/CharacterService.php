<?php

namespace App\Character;

use App\Character\Repository\CharacterRepository;
use App\CharacterOnlineTime\CharacterOnlineTimeService;
use App\Models\Character;
use Carbon\Carbon;
use Illuminate\Support\Collection;

readonly class CharacterService {
    public function __construct(
        private CharacterRepository $characterRepository,
        private CharacterOnlineTimeService $characterOnlineTimeService,
    ) {}

    public function retrieveOnlinePlayers(?string $guildName = null): Collection {
        return $this->characterRepository->getOnlinePlayer($guildName);
    }

    public function updateCharacterType(string $characterName, string $type): void {
        $this->characterRepository->updateCharacterTypeUsingName($characterName, $type);
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

    public function setCharacterAsOffline(Character $character): void {
        $this->saveCharacterOnlineTime($character);

        $character->online_at = null;
        $character->is_online = false;
        $character->save();

    }

    public function setCharacterAsOnline(Character $character): void {
        $character->online_at = now();
        $character->is_online = true;
        $character->save();
    }

    public function findOrCreate(string $name, string $vocation, string $level, string $joiningDate, string $guildName): Character {
        $character = $this->characterRepository->firstCharacterByName($name);
        if ($character) {
            $this->update($character, $name, $vocation, $level, $joiningDate, $guildName);
            return $character;
        }

        return $this->create($name, $vocation, $level, $joiningDate, $guildName);
    }

    private function saveCharacterOnlineTime(Character $character): void {
        if (is_null($character->online_at)) {
            return;
        }
        $this->characterOnlineTimeService->create($character->id, $character->online_at);
    }

    public function setCharacterNotInAsOffline(Collection $characters, string $guildName): void {
        $this->characterRepository->setCharactersNotInAsOffline($characters, $guildName);
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

}
