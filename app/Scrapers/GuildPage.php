<?php

namespace App\Scrapers;

use App\Character\CharacterService;
use App\Character\StatusEnum;
use App\Character\VocationEnum;
use App\Scrapers\Exceptions\NotFoundStatusInPage;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PHPHtmlParser\Dom;

class GuildPage {
    public Collection $characters;
    public Collection $onlineCharacters;
    public Collection $offlineCharacters;
    public function __construct(private CharacterService $characterService,) {
        $this->characters = collect();
        $this->onlineCharacters = collect();
        $this->offlineCharacters = collect();
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
                return;
            }
            $cells = $htmlCharacter->find('td');
            try {
                $characterArray = $this->getCharacterFromTd($cells, $guildName);
                if ($characterArray['is_online']) {
                    $this->onlineCharacters->push($characterArray);
                    $this->characters->push($characterArray);
                    return;
                }
                $this->offlineCharacters->push($characterArray);
                $this->characters->push($characterArray);
            } catch (NotFoundStatusInPage $e) {
                Log::info('[Status Not Found Begin]');
                Log::info($e->getHtmlNode()->getAttributes());
                Log::info('[Status Not Found End]');
            } catch (\Exception $exception) {
                Log::error($exception->getMessage(), $exception->getTrace());
            }
        });

        $this->characterService->upsert($this->characters);
        $this->characterService->updateAllOfflines();
        $this->characterService->updateAllOnline();
        return $this;
    }

    private function removeSpace(string $word): string {
        return Str::replace('&nbsp;', ' ', $word);
    }

    private function getStatus(string $status): string {
        $status = Str::replace('<span class="green">', '', $status);
        $status = Str::replace('<span class="red">', '', $status);
        $status = Str::replace('</span>', '', $status);
        $status = Str::replace('<b>', '', $status);
        return Str::replace('</b>', '', $status);
    }

    public function getOnlineCharacters(): int {
        return $this->onlineCharacters->count();
    }

    public function getOfflineCharacters(): int {
        return $this->offlineCharacters->count();
    }

    private function getName(Dom\HtmlNode $cells): string {
        $name = $cells->find('a')->innerHtml();
        return $this->removeSpace($name);
    }

    function getCharacterFromTd(Dom\Collection $cells, ?string $guildName): array {
        return [
            'name' => $this->getName($cells[1]),
            'vocation' => VocationEnum::from($cells[2]->innerHtml()),
            'level' => $cells[3]->innerHtml(),
            'joining_date' => Carbon::createFromFormat('M d Y', $this->removeSpace($cells[4]->innerHtml())),
            'is_online' => $this->getStatus($cells[5]->innerHtml()) === StatusEnum::ONLINE->value,
            'guild_name' => $guildName
        ];
    }
}
