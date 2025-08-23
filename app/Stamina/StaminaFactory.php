<?php

namespace App\Stamina;

use Carbon\CarbonInterval;

class StaminaFactory {

    public static function buildFullStamina(): Stamina {
        return new Stamina(CarbonInterval::hours(42));
    }

    public static function buildOrangeStamina(): Stamina {
        return new Stamina(CarbonInterval::hours(39));
    }
}
