<?php

namespace App\CharacterOnlineTime;

use App\Models\CharacterOnlineTime;
use Illuminate\Support\Collection;

class CharacterOnlineTimeRepository {

    public function retrieveOnlineTimeByOnlineAt(): Collection {
        return CharacterOnlineTime
            ::whereBetween('online_at', '>', [now()->startOfDay(), now()->endOfDay()])
            ->get();
    }
}
