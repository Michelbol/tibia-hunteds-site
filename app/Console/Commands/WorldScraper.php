<?php

namespace App\Console\Commands;

use App\Character;
use App\Models\OnlineCharacters;
use App\StatusEnum;
use App\VocationEnum;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use PHPHtmlParser\Dom;

class WorldScraper extends Command {

    protected $signature = 'world-scraper';

    protected $description = 'Command description';

    public function handle(): void {
        try {
            $timestamp = Carbon::now()->timestamp;
            $html = Browsershot::url('https://www.tibia.com/community/?subtopic=guilds&page=view&GuildName=quelibraland&timestamp='.$timestamp)
                ->waitUntilNetworkIdle()
                ->bodyHtml();

            $dom = new Dom();
            $dom->load($html);

            $htmlCharacters = $dom->find('#guilds .TableContainer .TableContent tr');
            unset($htmlCharacters[0]);
            unset($htmlCharacters[count($htmlCharacters)]);
            $htmlCharacters->each(function (Dom\HtmlNode $htmlCharacter) {
                try {
                    $status = $htmlCharacter->find('.onlinestatus span')->innerHtml();
                    $status = Str::replace('<b>', '', $status);
                    $status = Str::replace('</b>', '', $status);
                    $name = $htmlCharacter->find('a')->innerHtml();
                    $name = Str::replace('&nbsp;', ' ', $name);
                    $vocation = $htmlCharacter->find('td')->offsetGet(2)->innerHtml();
                    $level = $htmlCharacter->find('td')->offsetGet(3)->innerHtml();
                    $joiningDate = $htmlCharacter->find('td')[4]->innerHtml();
                    $joiningDate = str_replace('&nbsp;', ' ', $joiningDate);
                    $character = $this->findOrCreateCharacter($name, $vocation, $level, $status, $joiningDate);
                    if ($status === StatusEnum::ONLINE->value) {
                        if ($character->is_online) {
                            return;
                        }
                        $this->setCharacterAsOnline($character);
                        return;
                    }
                    $this->setCharacterAsOffline($character);
                } catch (\Exception $exception) {
                    $this->info($exception->getMessage());
                }
            });
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    private function findOrCreateCharacter(string $name, string $vocation, string $level, string $status, string $joiningDate): Character {
        $character = Character::whereName($name)->first();
        if ($character) {
            return $character;
        }

        return $this->createCharacter($name, $vocation, $level, $status, $joiningDate);
    }

    private function createCharacter(string $name, string $vocation, string $level, string $status, string $joiningDate): Character {
        $character = new Character();
        $character->name = $name;
        $character->vocation = VocationEnum::from($vocation);
        $character->level = $level;
        $character->joining_date = Carbon::createFromFormat('M d Y', $joiningDate);
        $character->is_online = false;
        $character->online_at = null;
        $character->save();


        return $character;
    }

    private function setCharacterAsOffline(Character $character): void {
        $character->online_at = null;
        $character->is_online = false;
        $character->save();
    }

    private function setCharacterAsOnline(Character $character): void {
        $character->online_at = now();
        $character->is_online = true;
        $character->save();
    }
}
