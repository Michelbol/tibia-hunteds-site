<?php

namespace App\CharacterOnlineTime;

use App\Models\CharacterOnlineTime;
use Carbon\Carbon;

class CharacterOnlineTimeService {

    public function create(int $characterId, Carbon $onlineAt): CharacterOnlineTime {
        $characterOnlineTime = new CharacterOnlineTime();
        $characterOnlineTime->character_id = $characterId;
        $characterOnlineTime->online_at = $onlineAt;
        $characterOnlineTime->offline_at = now();
        $characterOnlineTime->save();
        return $characterOnlineTime;
    }
}
