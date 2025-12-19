<?php

namespace App\Character;

use App\Models\Character;
use Carbon\Carbon;

class CharacterBuilder {
    private Character $character;

    public function __construct() {
        $this->reset();
    }

    public function reset(): void {
        $this->character = new Character();
    }

    public function setName(string $name): void {
        $this->character->name = $name;
    }

    public function setVocation(VocationEnum $vocation): void {
        $this->character->vocation = $vocation->value;
    }

    public function setLevel(int $level): void {
        $this->character->level = $level;
    }

    public function setJoiningDate(Carbon $joiningDate): void {
        $this->character->joining_date = $joiningDate;
    }

    public function setIsOnline(bool $isOnline): void {
        $this->character->is_online = $isOnline;
    }

    public function setOnlineAt(Carbon $onlineAt): void {
        $this->character->online_at = $onlineAt;
    }

    public function setPositionTime(?Carbon $positionTime): void {
        $this->character->position_time = $positionTime;
    }

    public function setPosition(?string $position): void {
        $this->character->position = $position;
    }

    public function setGuildName(string $guildName): void {
        $this->character->guild_name = $guildName;
    }

    public function getCharacter(): Character {
        $character = $this->character;
        $this->reset();
        return $character;
    }
}
