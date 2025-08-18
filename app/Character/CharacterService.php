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

    public function retrieveOnlinePlayers(): Collection {
        return $this->characterRepository->getOnlinePlayer();
    }

    public function updateCharacterType(string $characterName, string $type): void {
        $this->characterRepository->updateCharacterUsingName($characterName, $type);
    }

    public function create(string $name, string $vocation, int $level, string $joiningDate): Character {
        $character = new Character();
        $character->name = $name;
        $character->vocation = VocationEnum::from($vocation);
        $character->level = $level;
        $character->joining_date = Carbon::createFromFormat('M d Y', $joiningDate);
        $character->is_online = false;
        $character->online_at = null;
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

    public function findOrCreate(string $name, string $vocation, string $level, string $joiningDate): Character {
        $character = $this->characterRepository->firstCharacterByName($name);
        if ($character) {
            return $character;
        }

        return $this->create($name, $vocation, $level, $joiningDate);
    }

    private function saveCharacterOnlineTime(Character $character): void {
        if (is_null($character->online_at)) {
            return;
        }
        $this->characterOnlineTimeService->create($character->id, $character->online_at);
    }

    public function setCharacterNotInAsOffline(Collection $characters): void {
        $this->characterRepository->setCharactersNotInAsOffline($characters);
    }

}
