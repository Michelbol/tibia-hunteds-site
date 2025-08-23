<?php

namespace App\Stamina;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use PHPUnit\Framework\TestCase;

class StaminaCalculatorTest extends TestCase {

    public static function provideStaminaAndExpectedHourToBeFull(): iterable {
        yield [CarbonInterval::createFromFormat('H:i', '41:30'), Carbon::now()->addHours(3)];
    }


    public function testCalculateHourToBeFull_ShouldWorkProperly(CarbonInterval $stamina, Carbon $expectedHourToBeFull): void {
        $staminaCalculator = new StaminaCalculator();
        $hourToBeFull = $staminaCalculator->calculateHourToBeFull(new Stamina($stamina));

        $this->assertEquals($hourToBeFull->toDateTimeString(), $expectedHourToBeFull->toDateString());
    }
}
