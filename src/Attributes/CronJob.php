<?php

namespace App\Attributes;

use Attribute;

#[Attribute]
final class CronJob {
    public function __construct(private readonly string $period) {
    }

    public function getPeriod(): string {
        return $this->period;
    }
}
