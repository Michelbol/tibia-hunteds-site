<?php

namespace App\Scrapers;

use App\Character\CharacterService;
use App\Character\StatusEnum;
use App\Models\Character;
use App\Scrapers\Exceptions\NotFoundStatusInPage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PHPHtmlParser\Dom;

class GuildPage {
    public Collection $characters;
    public function __construct(private CharacterService $characterService,) {
        $this->characters = collect();
    }

    public function scrap(string $html, ?string $guildName = ''): self {
        $dom = new Dom();
        $dom->load($html);

        $htmlCharacters = $dom->find('#guilds .TableContainer .TableContent tr');
        unset($htmlCharacters[0]);
        unset($htmlCharacters[count($htmlCharacters)]);
        $htmlCharacters->each(function (Dom\HtmlNode $htmlCharacter) use ($guildName) {
            $trAttributes = $htmlCharacter->getAttributes();
            $isInvitationBoard = $htmlCharacter->find('.DoNotBreak');
            if ($isInvitationBoard->count() > 0 || (isset($trAttributes['class']) && $trAttributes['class'] === 'LabelH')) {
                Log::info('é label ou invitation board');
                return;
            }
            try {
                $status = $this->extractStatusFromTr($htmlCharacter);
                $name = $this->extractNameFromTr($htmlCharacter);
                $vocation = $this->extractVocationFromTr($htmlCharacter);
                $level = $this->extractLevelFromTr($htmlCharacter);
                $joiningDate = $this->extractJoiningDateFromTr($htmlCharacter);
                $character = $this->characterService->findOrCreate($name, $vocation, $level, $joiningDate, $guildName);
                $this->characters->push($character);
                $this->updateCharacterStatus($character, $status);
            } catch (NotFoundStatusInPage $e) {
                Log::info('[Status Not Found Begin]');
                Log::info($e->getHtmlNode()->getAttributes());
                Log::info('[Status Not Found End]');
            } catch (\Exception $exception) {
                Log::error($exception->getMessage(), $exception->getTrace());
            }
        });
        $this->characterService->setCharacterNotInAsOffline($this->characters, $guildName);
        return $this;
    }

    private function removeSpace(string $word): string {
        return Str::replace('&nbsp;', ' ', $word);
    }

    private function getStatus(string $status): string {
        $status = Str::replace('<b>', '', $status);
        return Str::replace('</b>', '', $status);
    }

    private function extractStatusFromTr(Dom\HtmlNode $htmlCharacter): string {
        $span = $htmlCharacter->find('.onlinestatus span');
        if ($span->count() === 0) {
            throw new NotFoundStatusInPage($htmlCharacter);
        }
        return $this->getStatus($span->innerHtml());
    }

    private function extractNameFromTr(Dom\HtmlNode $htmlCharacter): string {
        return $this->removeSpace($htmlCharacter->find('a')->innerHtml());
    }
    private function extractVocationFromTr(Dom\HtmlNode $htmlCharacter): string {
        return $htmlCharacter->find('td')->offsetGet(2)->innerHtml();
    }

    private function extractLevelFromTr(Dom\HtmlNode $htmlCharacter): string {
        return $htmlCharacter->find('td')->offsetGet(3)->innerHtml();
    }
    private function extractJoiningDateFromTr(Dom\HtmlNode $htmlCharacter): string {
        return $this->removeSpace($htmlCharacter->find('td')[4]->innerHtml());
    }

    private function updateCharacterStatus(Character $character, string $status): void {
        if ($status === StatusEnum::OFFLINE->value) {
            $this->characterService->setCharacterAsOffline($character);
            return;
        }
        if ($character->is_online) {
            return;
        }
        $this->characterService->setCharacterAsOnline($character);
    }

    public function getOnlineCharacters(): int {
        return $this->characters->reduce(function (?int $carry, Character $character) {
            if ($character->is_online) {
                $carry++;
            }
            return $carry;
        });
    }

    public function getOfflineCharacters(): int {
        return $this->characters->reduce(function (?int $carry, Character $character) {
            if (!$character->is_online) {
                $carry++;
            }
            return $carry;
        });
    }
}
