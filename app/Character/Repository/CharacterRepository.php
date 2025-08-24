<?php

namespace App\Character\Repository;

use App\Models\Character;
use Illuminate\Support\Collection;

class CharacterRepository {

    public function getOnlinePlayer(?string $guildName = null): Collection {
        $query = Character::query()
        ->where('is_online', true);

        if (!is_null($guildName)) {
            $query->where('guild_name', $guildName);
        }

        return $query->get();
    }

    public function updateCharacterTypeUsingName(string $characterName, string $type): void {
        Character::where('name', $characterName)->update(['type' => $type]);
    }

    public function firstCharacterByName(string $name): ?Character {
        return Character::where('name', $name)->first();
    }

    public function setCharactersNotInAsOffline(Collection $characters, string $guildName): void {
        Character
            ::whereNotIn('id', $characters->pluck('id'))
            ->where('guild_name', $guildName)
            ->update(['is_online' => false]);
    }

    public function updateCharacterPositionUsingName(string $characterName, string $position): void {
        Character::where('name', $characterName)->update(['position' => $position]);
    }
}
