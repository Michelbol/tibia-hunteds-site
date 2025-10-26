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

    public function updateAllOfflineAsOnlineAtNull(): void {
        Character::where('is_online', false)->update([
            'online_at' => null,
            'position' => null,
            'position_time' => null,
        ]);
    }

    public function updateSetOnlineAtNowForAllOnlinePlayersWithoutOnlineAt(): void {
        Character
            ::where('is_online', true)
            ->where('online_at', null)
            ->update([
                'online_at' => now(),
                'position' => null,
                'position_time' => null,
            ]);
    }

    public function upsertCharacters(Collection $characters): void {
        Character::upsert(
            $characters->toArray(),
            ['name'],
            ['name', 'vocation', 'level', 'joining_date', 'is_online', 'guild_name'],
        );
    }

    public function updateAllCharactersAsOffline(): void {
        Character::update([
            'online_at' => null,
            'is_online' => false,
            'position' => null,
            'position_time' => null,
        ]);
    }
}
