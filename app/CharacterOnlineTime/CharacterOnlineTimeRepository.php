<?php

namespace App\CharacterOnlineTime;

use App\Models\CharacterOnlineTime;
use App\Support\MysqlDateRange;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CharacterOnlineTimeRepository {

    public function retrieveOnlineTimeByOnlineAt(MysqlDateRange $range): Collection {
        return CharacterOnlineTime
            ::join('characters', 'character_online_times.character_id', 'characters.id')
            ->select(
                'character_online_times.character_id',
                'characters.name',
                'character_online_times.online_at as start',
                'character_online_times.offline_at as end',
                DB::raw('TIMESTAMPDIFF(MINUTE, character_online_times.online_at, character_online_times.offline_at) as duration_minutes')
            )
            ->whereBetween('character_online_times.online_at', $range->toMysqlBetween())
            ->orderBy('character_online_times.character_id')
            ->orderBy('character_online_times.online_at')
            ->get();
    }
}
