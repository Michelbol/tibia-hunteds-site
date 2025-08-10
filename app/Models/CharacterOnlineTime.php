<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Carbon $online_at
 * @property Carbon $offline_at
 * @property int $character_id
 */
class CharacterOnlineTime extends Model {

    protected $casts = [
        'online_at' => 'datetime',
        'offline_at' => 'datetime',
    ];
}
