<?php

namespace App\Doctrine\DBAL\Types\Purchase\Component;

use App\Doctrine\DBAL\Types\EnumType;

final class AttributeType extends EnumType {
    public const TYPE_BOOL = 'bool';
    public const TYPE_COLOR = 'color';
    public const TYPE_INT = 'int';
    public const TYPE_PERCENT = 'percent';
    public const TYPE_TEXT = 'text';
    public const TYPE_UNIT = 'unit';
    public const TYPES = [
        self::TYPE_BOOL,
        self::TYPE_COLOR,
        self::TYPE_INT,
        self::TYPE_PERCENT,
        self::TYPE_TEXT,
        self::TYPE_UNIT
    ];

    public function getName(): string {
        return 'attribute';
    }
}
