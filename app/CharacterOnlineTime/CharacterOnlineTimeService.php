<?php

namespace App\CharacterOnlineTime;

use App\Models\CharacterOnlineTime;
use Carbon\Carbon;
use Illuminate\Support\Collection;

readonly class CharacterOnlineTimeService {

    public function __construct(
        private CharacterOnlineTimeRepository $characterOnlineTimeRepository
    ) {}

    public function create(int $characterId, Carbon $onlineAt): CharacterOnlineTime {
        $characterOnlineTime = new CharacterOnlineTime();
        $characterOnlineTime->character_id = $characterId;
        $characterOnlineTime->online_at = $onlineAt;
        $characterOnlineTime->offline_at = now();
        $characterOnlineTime->save();
        return $characterOnlineTime;
    }

    public function retrieveOnlineTimeByOnlineAt(): Collection {
        return $this->characterOnlineTimeRepository->retrieveOnlineTimeByOnlineAt();
    }
}
