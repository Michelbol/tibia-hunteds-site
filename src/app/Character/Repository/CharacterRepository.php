<?php

namespace App\Character\Repository;

use Illuminate\Support\Collection;

interface CharacterRepository {

    public function getOnlinePlayer(?string $guildName = null): Collection;
}
