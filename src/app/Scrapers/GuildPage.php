<?php

namespace App\Scrapers;

use App\Character\CharacterService;
use App\Character\GuildPageCharacter;
use App\Models\Character;
use App\Scrapers\Exceptions\NotFoundStatusInPage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GuildPage {
    public Collection $onlineDatabaseCharacters;
    public Collection $onlineCharacters;
    public Collection $offlineDatabaseCharacters;
    public function __construct(private CharacterService $characterService,) {
        $this->onlineDatabaseCharacters = collect();
        $this->onlineCharacters = collect();
        $this->offlineDatabaseCharacters = collect();
    }

    public function scrap(string $html, ?string $guildName = ''): self {
        $dom2 = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom2->loadHTML($html);
        libxml_clear_errors();
        $xpath = new \DOMXPath($dom2);
        $guildCharacters = $this->characterService->getAllCharactersByGuildName($guildName);

        $trs = $this->retrieveTrs($xpath);
        foreach ($trs as $tr) {
            $classes = $tr->getAttribute('class');
            if (str_contains($classes, 'LabelH')) {
                continue;
            }
            $isInvitationBoard = $xpath->query(".//*[contains(@class, 'DoNotBreak')]", $tr);
            if ($isInvitationBoard->count() > 0){
                continue;
            }
            $text = trim($tr->textContent);
            if (Str::contains($text, 'No invited characters')) {
                continue;
            }
            $tds = $xpath->query("./td", $tr);
            try {
                $guildPageCharacter = GuildPageCharacter::buildFromDOMDocument($tds, $guildName);
                $databaseCharacter = $guildCharacters->first(function (Character $character) use ($guildPageCharacter) {
                    return $character->name === $guildPageCharacter->name;
                });
                if ($guildPageCharacter->is_online) {
                    if ($databaseCharacter === null) {
                        $databaseCharacter = $this->characterService->createByGuildPageCharacter($guildPageCharacter, $guildName);
                    }
                    $guildPageCharacter->online_at = $databaseCharacter->online_at;
                    if ($databaseCharacter->is_online === false) {
                        $guildPageCharacter->online_at = now();
                    }
                    $this->onlineCharacters->push($guildPageCharacter->toArray());
                    $this->onlineDatabaseCharacters->push($databaseCharacter);
                    continue;
                }
                $this->offlineDatabaseCharacters->push($databaseCharacter);
            } catch (NotFoundStatusInPage $e) {
                Log::info('[Status Not Found Begin]');
                Log::info($e->getHtmlNode()->getAttributes());
                Log::info('[Status Not Found End]');
            } catch (\Exception $exception) {
                Log::error($exception->getMessage());
            }
        }

        if (sizeof($trs) === 0) {
            return $this;
        }
        $onlineCharactersId = $this->onlineDatabaseCharacters->pluck('id');
        $this->characterService->upsert($this->onlineCharacters);
        Character
            ::whereNotIn('id', $onlineCharactersId)
            ->where('is_online', true)
            ->update([
                'is_online' => false,
                'online_at' => null,
                'position' => null,
                'position_time' => null,
            ]);
        Character::whereIn('id', $this->offlineDatabaseCharacters->pluck('id'))
            ->where('is_online', true)
            ->update([
                'is_online' => false,
                'online_at' => null,
                'position' => null,
                'position_time' => null,
            ]);
        $mergedHtmlCharacters = $this->onlineCharacters->merge($this->offlineDatabaseCharacters);
        $leaveGuildCharacters = $guildCharacters->whereNotIn('name', $mergedHtmlCharacters->pluck('name'));
        Character::destroy($leaveGuildCharacters);
        return $this;
    }

    public function getOnlineCharacters(): int {
        return $this->onlineDatabaseCharacters->count();
    }

    public function getOfflineCharacters(): int {
        return $this->offlineDatabaseCharacters->count();
    }

    private function retrieveTrs(\DOMXPath $xpath): mixed {
        return $xpath->query('//*[@id="guilds"]//*[contains(@class, "TableContainer")]//*[contains(@class, "TableContent")]//tr');
    }
}
