<?php

namespace App\Character\Repository;

use App\Models\Character;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class CharacterRepository {

    public function getOnlinePlayer(?string $guildName = null): Collection {
        $search = now()->subSeconds(2)->format('Y-m-d-H:i:s');
        $cache = $this->getSearch("online-$search.json");
        if (!is_null($cache) && !$cache->isEmpty()) {
            return $cache;
        }

        $query = Character::query()
        ->where('is_online', true);

        if (!is_null($guildName)) {
            $query->where('guild_name', $guildName);
        }
        $result = $query->get();
        $now = now()->format('Y-m-d-H:i:s');

        $this->saveSearch($now, $result);

        return $result;
    }

    public function updateCharacterTypeUsingName(string $characterName, string $type): void {
        Character::where('name', $characterName)->update(['type' => $type]);
    }

    public function firstCharacterByName(string $name): ?Character {
        return Character::where('name', $name)->first();
    }

    public function upsertCharacters(Collection $characters): void {
        Character::upsert(
            $characters->toArray(),
            ['name'],
            ['name', 'vocation', 'level', 'joining_date', 'is_online', 'guild_name', 'online_at'],
        );
    }

    public function getAllCharactersByGuildName(?string $guildName): Collection {
        return Character::where('guild_name', $guildName)->get();
    }

    public function updateCharacterIsAttacker(string $characterName, bool $isAttacker): void {
        Character::where('name', $characterName)->update(['is_attacker_character' => $isAttacker]);
    }

    private function saveSearch(string $now, Collection $result): void {
        Storage::disk('local')->put('online-' . $now . '.json', json_encode($result));
    }

    private function getSearch(string $fileName): ?Collection {
        $cache = Storage::disk('local')->get($fileName);
        if ($cache === false) {
            return null;
        }
        return collect(json_decode($cache));
    }
}
