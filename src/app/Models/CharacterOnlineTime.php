<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property Carbon $online_at
 * @property Carbon $offline_at
 * @property int $character_id
 * @property Carbon $created_at
 */
class CharacterOnlineTime extends Model {

    protected $casts = [
        'online_at' => 'datetime',
        'offline_at' => 'datetime',
        'start' => 'datetime',
        'end' => 'datetime',
    ];
}
