<?php

namespace App\Scrapers;

use App\Character\Factory\GuildPageCharacterFromDomDocument;
use App\Character\GuildPageCharacter;
use App\Models\Character;
use Illuminate\Support\Collection;

class GuildPageTrIterator {

    private ?\DOMElement $tr = null;
    private ?\DOMXPath $dom;
    private GuildPageCharacter $guildPageCharacter;

    public function __construct(\DOMXPath $dom, \DOMElement $tr = null) {
        if ($tr !== null) {
            $this->tr = $tr;
        }
        $this->dom = $dom;
    }

    public function isEmpty(): bool {
        return $this->tr !== null;
    }

    public function getElementByClass(string $class): \DOMNodeList {
        return $this->dom->query(".//*[contains(@class, '$class')]", $this->tr);
    }

    public function getElementByTagName(string $tag): \DOMNodeList {
        return $this->dom->query("./$tag", $this->tr);
    }

    public function isClassContains(string $class): bool {
        $classes = $this->tr->getAttribute('class');
        return str_contains($classes, $class);
    }

    public function buildGuildPageCharacter(string $guildName): GuildPageCharacter {
        $tds = $this->getElementByTagName('td');
        $guildPageCharacterBuilder = new GuildPageCharacterFromDomDocument($tds, $guildName);
        $this->guildPageCharacter = $guildPageCharacterBuilder->buildGuildPageCharacter();
        return $this->guildPageCharacter;
    }

    public function findDatabaseCharacter(Collection $databaseCharacter): ?Character {
        return $databaseCharacter->first(function (Character $character) {
            return $character->name === $this->guildPageCharacter->name;
        });
    }
}
