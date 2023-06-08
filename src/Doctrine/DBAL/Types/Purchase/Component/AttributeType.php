<?php

namespace App\Doctrine\DBAL\Types\Purchase\Component;

use App\Doctrine\DBAL\Types\EnumType;

final class AttributeType extends EnumType {
    final public const TYPE_BOOL = 'bool';
    final public const TYPE_COLOR = 'color';
    final public const TYPE_INT = 'int';
    final public const TYPE_PERCENT = 'percent';
    final public const TYPE_TEXT = 'text';
    final public const TYPE_MEASURE = 'measure';
    final public const TYPES = [
        self::TYPE_BOOL,
        self::TYPE_COLOR,
        self::TYPE_INT,
        self::TYPE_PERCENT,
        self::TYPE_TEXT,
        self::TYPE_MEASURE
    ];

    public function getName(): string {
        return 'attribute';
    }
}
