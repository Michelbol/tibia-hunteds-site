<?php

namespace App\Stamina;

use Carbon\CarbonInterval;

readonly class HuntTime {

    public function __construct(
        public CarbonInterval $startAt,
        public CarbonInterval $endAt
    ) {
    }

}
