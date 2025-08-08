<?php

namespace App\Models;

use App\Character;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $name
 * @property string $vocation
 * @property int $level
 * @property Carbon $joining_date
 * @property string $type
 * @property Carbon $created_at
 */
class OnlineCharacters extends Model {

    public static function createFromCharacter(Character $character): OnlineCharacters {
        $onlineCharacter = new OnlineCharacters();
        $onlineCharacter->name = $character->name;
        $onlineCharacter->vocation = $character->vocation;
        $onlineCharacter->level = $character->level;
        $onlineCharacter->joining_date = $character->joiningDate;
        $onlineCharacter->save();
        return $onlineCharacter;
    }

    public function toArray(): array {
        return [
            'name' => $this->name,
            'vocation' => $this->vocation,
            'level' => $this->level,
            'joining_date' => $this->joining_date,
            'type' => $this->type,
            'created_at' => $this->created_at->timezone('America/Sao_Paulo')->format('Y-m-d H:i:s'),
        ];
    }
}
