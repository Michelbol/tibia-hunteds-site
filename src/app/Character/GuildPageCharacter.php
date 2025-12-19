<?php

namespace App\Character;

use Carbon\Carbon;
use Illuminate\Support\Str;
use PHPHtmlParser\Dom\HtmlNode;

class GuildPageCharacter {

    public string $rank;
    public string $name;
    public VocationEnum $vocation;
    public int $level;
    public Carbon $joining_date;
    public bool $is_online;
    public string $guild_name;
    public ?Carbon $online_at = null;

    public function getJoiningDateFormated(): string {
        return $this->joining_date->format('M d Y');
    }

    public static function getName(HtmlNode $cells): string {
        $name = $cells->find('a')->innerHtml();
        return self::removeSpace($name);
    }

    public static function removeSpace(string $word): string {
        $word = str_replace(["\xC2\xA0", "\u{00A0}"], " ", $word); // remove NBSP
        $word = preg_replace('/\s+/u', ' ', $word);                // normaliza
        $word = Str::replace('&nbsp;', ' ', $word);
        return trim($word);
    }
    public static function getStatus(string $status): string {
        $status = Str::replace('<span class="green">', '', $status);
        $status = Str::replace('<span class="red">', '', $status);
        $status = Str::replace('</span>', '', $status);
        $status = Str::replace('<b>', '', $status);
        $status = Str::replace('</b>', '', $status);
        return Str::lower($status);
    }

    public function toArray(): array {
        $array = [
            'name' => $this->name,
            'vocation' => $this->vocation,
            'level' => $this->level,
            'joining_date' => $this->joining_date,
            'is_online' => $this->is_online,
            'guild_name' => $this->guild_name,
        ];
        if ($this->online_at !== null) {
            $array['online_at'] = $this->online_at;
        }
        return $array;
    }
}
