<?php

namespace App\Doctrine\DBAL\Types\Production\Engine;

use App\Doctrine\DBAL\Types\EnumType;

final class EngineType extends EnumType {
    final public const TYPE_COUNTER_PART = 'counter-part';
    final public const TYPE_TOOL = 'tool';
    final public const TYPE_WORKSTATION = 'workstation';
    final public const TYPE_MACHINE = 'machine';
    final public const TYPE_SPARE_PART = 'spare-part';
    final public const TYPE_INFRA = 'infra';
    final public const TYPE_INFORMATIQUE = 'informatique';
    final public const TYPES = [
        self::TYPE_COUNTER_PART,
        self::TYPE_TOOL,
        self::TYPE_WORKSTATION,
        self::TYPE_MACHINE,
        self::TYPE_SPARE_PART,
        self::TYPE_INFRA,
        self::TYPE_INFORMATIQUE
    ];

    public function getName(): string {
        return 'engine';
    }
}
