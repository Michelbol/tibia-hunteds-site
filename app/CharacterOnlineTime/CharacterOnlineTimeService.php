<?php

namespace App\CharacterOnlineTime;

use App\Models\CharacterOnlineTime;
use App\Support\MysqlDateRange;
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

    public function retrieveOnlineTimeByOnlineAt(Carbon $date): Collection {
        $mysqlBetween = MysqlDateRange::fromCarbons($date->copy()->startOfDay(), $date->copy()->endOfDay());
        $charOnline = $this->characterOnlineTimeRepository->retrieveOnlineTimeByOnlineAt($mysqlBetween);
        $groupedOnlineChars = $charOnline->groupBy('character_id');
        $mapperOnlineChars = collect();
        foreach ($groupedOnlineChars as $characterOnlineTime) {
            $mapperOnlineChars->push([
                'name' => $characterOnlineTime[0]->name,
                'sessions' => $characterOnlineTime,
            ]);
        }
        return $mapperOnlineChars;
    }
}
