<?php

namespace App\Scrapers;

use App\Character\CharacterService;
use App\Character\GuildPageCharacter;
use App\Models\Character;
use App\Scrapers\Exceptions\NotFoundStatusInPage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use PHPHtmlParser\Dom;

class GuildPage {
    public Collection $onlineDatabaseCharacters;
    public Collection $offlineDatabaseCharacters;
    public function __construct(private CharacterService $characterService,) {
        $this->onlineDatabaseCharacters = collect();
        $this->offlineDatabaseCharacters = collect();
    }

    public function scrap(string $html, ?string $guildName = ''): self {
        $dom = new Dom();
        $dom->load($html);
        $guildCharacters = $this->characterService->getAllCharactersByGuildName($guildName);

        $htmlCharacters = $dom->find('#guilds .TableContainer .TableContent tr');
        unset($htmlCharacters[0]);
        unset($htmlCharacters[count($htmlCharacters)]);
        $htmlCharacters->each(function (Dom\HtmlNode $htmlCharacter) use ($guildName, $guildCharacters) {
            $trAttributes = $htmlCharacter->getAttributes();
            $isInvitationBoard = $htmlCharacter->find('.DoNotBreak');
            if ($isInvitationBoard->count() > 0 || (isset($trAttributes['class']) && $trAttributes['class'] === 'LabelH')) {
                return;
            }
            $cells = $htmlCharacter->find('td');
            try {
                $guildPageCharacter = GuildPageCharacter::buildFromGuildPageCell($cells, $guildName);
                $databaseCharacter = $guildCharacters->first(function (Character $character) use ($guildPageCharacter) {
                    return $character->name === $guildPageCharacter->name;
                });
                if ($guildPageCharacter->is_online) {
                    if ($databaseCharacter === null) {
                        $this->characterService->createByGuildPageCharacter($guildPageCharacter, $guildName);
                    }
                    $this->onlineDatabaseCharacters->push($databaseCharacter);
                    return;
                }
                $this->offlineDatabaseCharacters->push($databaseCharacter);
            } catch (NotFoundStatusInPage $e) {
                Log::info('[Status Not Found Begin]');
                Log::info($e->getHtmlNode()->getAttributes());
                Log::info('[Status Not Found End]');
            } catch (\Exception $exception) {
                Log::error($exception->getMessage(), $exception->getTrace());
            }
        });

        $onlineCharactersId = $this->onlineDatabaseCharacters->pluck('id');
        Character
            ::whereIn('id', $onlineCharactersId)
            ->where('online_at', null)
            ->where('is_online', false)
        ->update([
            'is_online' => true,
            'online_at' => now(),
            'position' => null,
            'position_time' => null,
        ]);
        Character::whereNotIn('id', $onlineCharactersId)->update([
            'is_online' => false,
            'online_at' => null,
            'position' => null,
            'position_time' => null,]);
        Character::whereIn('id', $this->offlineDatabaseCharacters->pluck('id'))->update([
            'is_online' => false,
            'online_at' => null,
            'position' => null,
            'position_time' => null,]);
        return $this;
    }

    public function getOnlineCharacters(): int {
        return $this->onlineDatabaseCharacters->count();
    }

    public function getOfflineCharacters(): int {
        return $this->offlineDatabaseCharacters->count();
    }
}
