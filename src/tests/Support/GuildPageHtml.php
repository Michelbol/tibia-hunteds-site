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

    public static function listOfMultipleCharacters(array $characters): string {
        $rows = implode('', array_map(fn($c) => self::buildCharacterRow($c), $characters));
        $html = <<<HTML
        <div id="guilds">
            <div class="TableContainer">
                <table class="TableContent">
                    <tr class="LabelH">
                        <td>Rank</td>
                        <td>Name and Title</td>
                        <td>Vocation</td>
                        <td>Level</td>
                        <td>Joining Date</td>
                        <td>Status</td>
                    </tr>
                    $rows
                </table>
            </div>
        </div>
HTML;
        return $html;
    }

    public static function onlyLabelHRow(): string {
        return <<<HTML
        <div id="guilds">
            <div class="TableContainer">
                <table class="TableContent">
                    <tr class="LabelH">
                        <td>Rank</td>
                        <td>Name and Title</td>
                        <td>Vocation</td>
                        <td>Level</td>
                        <td>Joining Date</td>
                        <td>Status</td>
                    </tr>
                </table>
            </div>
        </div>
HTML;
    }

    public static function withDoNotBreakRow(): string {
        return <<<HTML
        <div id="guilds">
            <div class="TableContainer">
                <table class="TableContent">
                    <tr class="LabelH">
                        <td>Rank</td><td>Name</td><td>Vocation</td><td>Level</td><td>Joining Date</td><td>Status</td>
                    </tr>
                    <tr>
                        <td class="DoNotBreak">Invited Character</td>
                        <td>col2</td><td>col3</td><td>col4</td><td>col5</td><td>col6</td>
                    </tr>
                </table>
            </div>
        </div>
HTML;
    }

    public static function withNoInvitedCharactersText(): string {
        return <<<HTML
        <div id="guilds">
            <div class="TableContainer">
                <table class="TableContent">
                    <tr class="LabelH">
                        <td>Rank</td><td>Name</td><td>Vocation</td><td>Level</td><td>Joining Date</td><td>Status</td>
                    </tr>
                    <tr bgcolor="#F1E0C6">
                        <td>No invited characters</td>
                        <td>col2</td><td>col3</td><td>col4</td><td>col5</td><td>col6</td>
                    </tr>
                </table>
            </div>
        </div>
HTML;
    }

    public static function emptyTable(): string {
        return <<<HTML
        <div id="guilds">
            <div class="TableContainer">
                <table class="TableContent">
                </table>
            </div>
        </div>
HTML;
    }

    public static function listOfCharactersWithInvalidTdCount(): string {
        $invalidRow = '<tr bgcolor="#F1E0C6"><td>Leader</td><td>Only Four</td><td>Knight</td><td>100</td></tr>';
        $html = <<<HTML
        <div id="guilds">
            <div class="TableContainer">
                <table class="TableContent">
                    <tr class="LabelH"><td>Rank</td><td>Name</td><td>Vocation</td><td>Level</td><td>Joining Date</td><td>Status</td></tr>
                    $invalidRow
                </table>
            </div>
        </div>
HTML;
        return $html;
    }

    private static function buildOnlineTd(bool $isOnline): string {
        return $isOnline ?
            '<td class="onlinestatus"><span class="green"><b>online</b></span></td></tr>':
            '<td class="onlinestatus"><span class="red">offline</span></td>';
    }

}
