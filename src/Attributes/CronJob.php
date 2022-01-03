<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class CronJob {
    public function __construct(private string $period) {
    }

    public function getPeriod(): string {
        return $this->period;
    }
}
