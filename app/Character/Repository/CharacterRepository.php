<?php

namespace App\Character\Repository;

use App\Models\Character;
use Illuminate\Support\Collection;

class CharacterRepository {

    public function getOnlinePlayer(): Collection {
        return Character::where('is_online', true)->get();
    }

    public function updateCharacterUsingName(string $characterName, string $type): void {
        Character::where('name', $characterName)->update(['type' => $type]);
    }

    public function firstCharacterByName(string $name): ?Character {
        return Character::where('name', $name)->first();
    }
}
