<?php

namespace App\Doctrine\DBAL\Types\Production\Engine;

use App\Doctrine\DBAL\Types\EnumType;

final class EngineType extends EnumType {
    final public const TYPE_COUNTER_PART = 'counter-part';
    final public const TYPE_TOOL = 'tool';
    final public const TYPE_WORKSTATION = 'workstation';
    final public const TYPES = [
        self::TYPE_COUNTER_PART,
        self::TYPE_TOOL,
        self::TYPE_WORKSTATION
    ];

    public function getName(): string {
        return 'engine';
    }
}
