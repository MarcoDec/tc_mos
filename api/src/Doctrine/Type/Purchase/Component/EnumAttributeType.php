<?php

declare(strict_types=1);

namespace App\Doctrine\Type\Purchase\Component;

use App\Collection;

enum EnumAttributeType: string {
    case TYPE_BOOL = 'bool';
    case TYPE_COLOR = 'color';
    case TYPE_INT = 'int';
    case TYPE_PERCENT = 'percent';
    case TYPE_TEXT = 'text';
    case TYPE_UNIT = 'unit';

    /** @var string */
    public const DEFAULT = 'text';

    /** @var string[] */
    public const ENUM = ['bool', 'color', 'int', 'percent', 'text', 'unit'];

    /** @return string[] */
    public static function values(): array {
        return (new Collection(self::cases()))->map(static fn (self $case): string => $case->value)->toArray();
    }
}
