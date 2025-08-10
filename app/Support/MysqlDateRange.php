<?php

namespace App\Support;

use Carbon\Carbon;

class MysqlDateRange {
    protected Carbon $start;
    protected Carbon $end;

    public function __construct(Carbon $start, Carbon $end) {
        $this->start = $start->copy()->startOfSecond();
        $this->end = $end->copy()->endOfSecond();
    }
    public static function fromCarbons(Carbon $start, Carbon $end): self {
        return new self($start, $end);
    }

    public function toMysqlBetween(): array {
        return [
            $this->start->toDateTimeString(),
            $this->end->toDateTimeString()
        ];
    }
}
