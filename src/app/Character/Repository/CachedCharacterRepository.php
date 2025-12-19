<?php

namespace App\Character\Repository;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class CachedCharacterRepository implements CharacterRepository {

    public function __construct(
        private readonly DatabaseCharacterRepository $databaseCharacterRepository,
    ) {}

    public function getOnlinePlayer(?string $guildName = null): Collection {
        $cache = $this->getSearch();
        if (!is_null($cache) && !$cache->isEmpty()) {
            return $cache;
        }
        return $this->databaseCharacterRepository->getOnlinePlayer($guildName);
    }

    private function getSearch(): ?Collection {
        $search = now()->subSeconds(2)->format('Y-m-d-H-i-s');
        $cache = Storage::disk('local')->get("/cache/online-characters/online-$search.json");
        if ($cache === false) {
            return null;
        }
        return collect(json_decode($cache));
    }
}
