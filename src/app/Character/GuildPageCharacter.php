<?php

namespace App\Character;

use Carbon\Carbon;
use Illuminate\Support\Str;
use PHPHtmlParser\Dom\Collection;
use PHPHtmlParser\Dom\HtmlNode;

class GuildPageCharacter {

    public string $rank;
    public string $name;
    public VocationEnum $vocation;
    public int $level;
    public Carbon $joining_date;
    public bool $is_online;
    public string $guild_name;

    public static function buildFromGuildPageCell(Collection $cells, string $guildName): self {
        $guildPageCharacter = new self();
        $guildPageCharacter->name = self::getName($cells[1]);
        $guildPageCharacter->vocation = VocationEnum::from($cells[2]->innerHtml());
        $guildPageCharacter->level = $cells[3]->innerHtml();
        $guildPageCharacter->joining_date = Carbon::createFromFormat('M d Y', self::removeSpace($cells[4]->innerHtml()));
        $guildPageCharacter->is_online = self::getStatus($cells[5]->innerHtml()) === StatusEnum::ONLINE->value;
        $guildPageCharacter->guild_name = $guildName;
        return $guildPageCharacter;
    }

    public function getJoiningDateFormated(): string {
        return $this->joining_date->format('M d Y');
    }

    private static function getName(HtmlNode $cells): string {
        $name = $cells->find('a')->innerHtml();
        return self::removeSpace($name);
    }

    private static function removeSpace(string $word): string {
        return Str::replace('&nbsp;', ' ', $word);
    }
    private static function getStatus(string $status): string {
        $status = Str::replace('<span class="green">', '', $status);
        $status = Str::replace('<span class="red">', '', $status);
        $status = Str::replace('</span>', '', $status);
        $status = Str::replace('<b>', '', $status);
        $status = Str::replace('</b>', '', $status);
        return Str::lower($status);
    }
}
