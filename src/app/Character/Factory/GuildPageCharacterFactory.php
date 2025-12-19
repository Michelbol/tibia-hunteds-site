<?php

namespace App\Character\Factory;

use App\Character\GuildPageCharacter;

interface GuildPageCharacterFactory {

    public function buildGuildPageCharacter(): GuildPageCharacter;
}
