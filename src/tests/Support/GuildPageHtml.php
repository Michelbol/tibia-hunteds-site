<?php

namespace Tests\Support;

use App\Character\GuildPageCharacter;

class GuildPageHtml {

    public static function listOfCharacters(GuildPageCharacter $guildPageCharacter): string {
        $characterTr = self::buildCharacterRow($guildPageCharacter);
        $html = <<<HTML
        <div id="guilds">
            <div class="TableContainer">
                <div class="CaptionContainer">
                    <div class="Text">Guild Members</div>
                </div>
                <table class="TableContent">
                    <tr class="LabelH">
                        <td>Rank</td>
                        <td>Name and Title</td>
                        <td>Vocation</td>
                        <td>Level</td>
                        <td>Joining Date</td>
                        <td>Status</td>
                    </tr>
                    $characterTr
                </table>
            </div>
            <div class="TableContainer">
                <div class="CaptionContainer">
                    <div class="Text">Invited Characters</div>
                </div>
                <table class="TableContent">
                    <tr bgcolor="#F1E0C6"><td>No invited characters found.</td></tr>
                </table>
            </div>
        </div>
HTML;

        return $html;
    }

    private static function buildCharacterRow(GuildPageCharacter $guildPageCharacter): string {
        $onlineTd = self::buildOnlineTd($guildPageCharacter->is_online);
        $vocation = $guildPageCharacter->vocation->value;
        $joiningDate = $guildPageCharacter->getJoiningDateFormated();
        return <<<HTML
<tr bgcolor="#F1E0C6"><td>$guildPageCharacter->rank</td>
    <td><a href="https://www.tibia.com/community/?subtopic=characters&amp;name=Fael+Fahurr">$guildPageCharacter->name</a></td>
    <td>$vocation</td>
    <td>$guildPageCharacter->level</td>
    <td>$joiningDate</td>
    $onlineTd
</tr>
HTML;
    }

    private static function buildOnlineTd(bool $isOnline): string {
        return $isOnline ?
            '<td class="onlinestatus"><span class="green"><b>online</b></span></td></tr>':
            '<td class="onlinestatus"><span class="red">offline</span></td>';
    }

}
