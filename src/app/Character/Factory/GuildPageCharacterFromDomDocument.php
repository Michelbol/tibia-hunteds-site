<?php

namespace App\Character\Factory;

use App\Character\GuildPageCharacter;
use App\Character\StatusEnum;
use App\Character\VocationEnum;
use Carbon\Carbon;

class GuildPageCharacterFromDomDocument implements GuildPageCharacterFactory {

    public function __construct(
        private readonly \DOMNodeList $DOMElement,
        private readonly string $guildName,
    ) {}

    public function buildGuildPageCharacter(): GuildPageCharacter {
        $guildPageCharacter = new GuildPageCharacter();
        $guildPageCharacter->name = GuildPageCharacter::removeSpace($this->DOMElement->item(1)->textContent);
        $guildPageCharacter->vocation = VocationEnum::from($this->DOMElement->item(2)->textContent);
        $guildPageCharacter->level = (int)$this->DOMElement->item(3)->textContent;
        $guildPageCharacter->joining_date = Carbon::createFromFormat('M d Y', GuildPageCharacter::removeSpace($this->DOMElement->item(4)->textContent));
        $guildPageCharacter->is_online = GuildPageCharacter::getStatus($this->DOMElement->item(5)->textContent) === StatusEnum::ONLINE->value;
        $guildPageCharacter->guild_name = $this->guildName;
        return $guildPageCharacter;
    }
}
