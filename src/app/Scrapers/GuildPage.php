<?php

namespace App\Scrapers;

use App\Character\CharacterService;
use App\Models\Character;
use App\Scrapers\Exceptions\NotFoundStatusInPage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GuildPage {
    private static ?GuildPage $instance;
    public Collection $onlineDatabaseCharacters;
    public Collection $onlineCharacters;
    public Collection $offlineDatabaseCharacters;
    private CharacterService $characterService;

    private function __construct(
        private string $html,
        private ?string $guildName = ''
    ) {
        $this->onlineDatabaseCharacters = collect();
        $this->onlineCharacters = collect();
        $this->offlineDatabaseCharacters = collect();
        $this->characterService = app(CharacterService::class);
    }

    public static function getInstance(string $html, ?string $guildName = ''): self {
        if (!isset(self::$instance)) {
            self::$instance = new GuildPage($html, $guildName);
        }
        return self::$instance;
    }

    public static function reset(): void {
        self::$instance = null;
    }

    public function scrap(): self {
        $dom2 = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom2->loadHTML($this->html);
        libxml_clear_errors();
        $xpath = new \DOMXPath($dom2);
        $guildCharacters = $this->characterService->getAllCharactersByGuildName($this->guildName);

        $trs = $this->retrieveTrs($xpath);

        foreach ($trs as $tr) {
            $guildPageTrIterator = new GuildPageTrIterator($xpath, $tr);
            if ($guildPageTrIterator->isClassContains('LabelH')) {
                continue;
            }
            $isInvitationBoard = $guildPageTrIterator->getElementByClass('DoNotBreak');
            if ($isInvitationBoard->count() > 0){
                continue;
            }
            $text = trim($tr->textContent);
            if (Str::contains($text, 'No invited characters')) {
                continue;
            }

            try {
                $guildPageCharacter = $guildPageTrIterator->buildGuildPageCharacter($this->guildName);
                $databaseCharacter = $guildPageTrIterator->findDatabaseCharacter($guildCharacters);
                if ($guildPageCharacter->is_online) {
                    if ($databaseCharacter === null) {
                        $databaseCharacter = $this->characterService->findCharacterByName($guildPageCharacter->name);
                        if ($databaseCharacter === null) {
                            $databaseCharacter = $this->characterService->createByGuildPageCharacter($guildPageCharacter);
                        }
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
