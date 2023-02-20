<?php

declare(strict_types=1);

namespace App\Doctrine\Type\Hr\Employee\Event;

use App\Collection;

enum EnumEmployeeEventType: string {
    case TYPE_AGREED = 'agreed';
    case TYPE_WARNING = 'warning';

    /** @var string[] */
    public const ENUM = ['agreed', 'warning'];

    /** @return string[] */
    public static function values(): array {
        return (new Collection(self::cases()))->map(static fn (self $case): string => $case->value)->toArray();
    }
}
