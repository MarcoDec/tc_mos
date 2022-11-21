<?php

declare(strict_types=1);

namespace App\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class CronJob {
    public function __construct(private readonly string $period) {
    }

    public function getPeriod(): string {
        return $this->period;
    }
}
