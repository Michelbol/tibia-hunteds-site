<?php

namespace App\Stamina;

use Carbon\CarbonInterval;

class HoursToBeFull {

    public function __construct(
      public CarbonInterval $greenHours,
      public CarbonInterval $orangeHours,
    ){}
}
