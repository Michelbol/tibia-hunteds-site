<?php

namespace App\Character\Factory;

use App\Character\GuildPageCharacter;
use App\Character\StatusEnum;
use App\Character\VocationEnum;
use Carbon\Carbon;
use PHPHtmlParser\Dom\Collection;

class GuildPageCharacterFromPageCell implements GuildPageCharacterFactory {

    public function __construct(
        private readonly Collection $cells,
        private readonly string $guildName
    ) {}

    public function buildGuildPageCharacter(): GuildPageCharacter {
        $guildPageCharacter = new GuildPageCharacter();
        $guildPageCharacter->name = GuildPageCharacter::getName($this->cells[1]);
        $guildPageCharacter->vocation = VocationEnum::from($this->cells[2]->innerHtml());
        $guildPageCharacter->level = $this->cells[3]->innerHtml();
        $guildPageCharacter->joining_date = Carbon::createFromFormat('M d Y', GuildPageCharacter::removeSpace($this->cells[4]->innerHtml()));
        $guildPageCharacter->is_online = GuildPageCharacter::getStatus($this->cells[5]->innerHtml()) === StatusEnum::ONLINE->value;
        $guildPageCharacter->guild_name = $this->guildName;
        return $guildPageCharacter;
    }
}
