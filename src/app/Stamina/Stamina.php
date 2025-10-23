<?php

namespace App\Stamina;

use Carbon\CarbonInterval;

class Stamina {

    public function __construct(
        public CarbonInterval $currentStamina
    ) {}


    public function qtdGreenHoursToBeFull(): CarbonInterval {
        if ($this->currentStamina->hours > 39) {
            $staminaFull = StaminaFactory::buildFullStamina();
            $diffInSeconds = abs($staminaFull->currentStamina->totalSeconds - $this->currentStamina->totalSeconds);
            return CarbonInterval::seconds($diffInSeconds);
        }
        return CarbonInterval::hours(3);
    }

    public function qtdOrangeHoursToBeFull(): CarbonInterval {
        if ($this->currentStamina->hours < 39) {
            $orangeStamina = StaminaFactory::buildOrangeStamina();
            $diffInSeconds = abs($orangeStamina->currentStamina->totalSeconds - $this->currentStamina->totalSeconds);
            return CarbonInterval::seconds($diffInSeconds);
        }
        return CarbonInterval::hours(0);
    }
}
