<?php

namespace App\Stamina;

use Carbon\Carbon;
use Carbon\CarbonInterval;

class StaminaCalculator {

    private const GREEN_STAMINA = 3;
    private const ORANGE_STAMINA = 39;

    public function calculateMissingHuntingTime(): void {
        $startTime = CarbonInterval::hours(6);
        $endTime = CarbonInterval::hours(9);
        $huntTime = new HuntTime($startTime, $endTime);
        $currentStamina = new Stamina(CarbonInterval::hours(40)->minutes(48));
        $now = Carbon::now();

        $greenHoursToRecover = $currentStamina->qtdGreenHoursToBeFull();
        $orangeHoursToRecover = $currentStamina->qtdOrangeHoursToBeFull();
        $hoursToBeFull = $now->copy()->addSeconds($greenHoursToRecover->totalSeconds)->add($orangeHoursToRecover->totalSeconds);
        dd('Seconds Green '. $greenHoursToRecover->totalSeconds. ' Secondss Orange '. $orangeHoursToRecover->totalSeconds);

        $fullStamina = CarbonInterval::createFromFormat('H:i:s', '42:00:00');
        $hoursToBeFull = $currentStamina->currentStamina->diff($fullStamina);
        // 00:45 min
        $hoursToBeFull = $this->calculateHoursToBeFull();


        // 19:00:00
        $diffNowToTimeToHunt = $now->diff($huntTime->startAt);
        $greenHours = $this->convertTimeToGreenStamina($diffNowToTimeToHunt);
        // 11 horas pra inicio da hunt
        // X horas para recuperar stamina


        // Faltam x horas para o inicio da Hunt
        // Faltam caçar X horas no seu boneco
        // Stamina ficará full as X horas se vc não caçar

    }

    public function calculateHourToBeFull(Stamina $stamina): Carbon {
        return Carbon::now()->add($stamina->qtdGreenHoursToBeFull())->add($stamina->qtdOrangeHoursToBeFull());
    }

    private function convertTimeToGreenStamina(CarbonInterval $time): CarbonInterval {
        return $time->divide(6);
    }
}
