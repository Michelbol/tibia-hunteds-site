<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $vocation
 * @property string $guild_name
 * @property int $level
 * @property \Illuminate\Support\Carbon $joining_date
 * @property string $type
 * @property boolean $is_online
 * @property Carbon $online_at
 * @property string $position
 * @property Carbon $position_time
 * @property Carbon $created_at
 */
class Character extends Model {

    protected $casts = [
        'online_at' => 'datetime',
        'position_time' => 'datetime',
    ];

    public function toArray(): array {
        return [
            'name' => $this->name,
            'vocation' => $this->vocation,
            'level' => $this->level,
            'joining_date' => $this->joining_date,
            'type' => $this->type,
            'online_at' => $this->online_at ? $this->online_at->timezone('America/Sao_Paulo')->format('Y-m-d H:i:s') : null,
            'position_time' => $this->position_time ? $this->position_time->timezone('America/Sao_Paulo')->format('Y-m-d H:i:s') : null,
            'position' => $this->position,
        ];
    }
}
