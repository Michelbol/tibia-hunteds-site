<?php

namespace App\Character;

use App\Models\Character;

class GuildPageCharacterAdapter {

    public function convertGuildPageCharacterIntoCharacter(GuildPageCharacter $guildPageCharacter): Character {
        $characterBuilder = new CharacterBuilder();
        $characterBuilder->setName($guildPageCharacter->name);
        $characterBuilder->setVocation($guildPageCharacter->vocation);
        $characterBuilder->setLevel($guildPageCharacter->level);
        $characterBuilder->setJoiningDate($guildPageCharacter->joining_date);
        $characterBuilder->setIsOnline($guildPageCharacter->is_online);
        $characterBuilder->setGuildName($guildPageCharacter->guild_name);
        $characterBuilder->setOnlineAt(now());
        $characterBuilder->setPosition(null);
        $characterBuilder->setPositionTime(null);
        return $characterBuilder->getCharacter();
    }
}
