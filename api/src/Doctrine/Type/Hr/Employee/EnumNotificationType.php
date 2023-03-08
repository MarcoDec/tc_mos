<?php

declare(strict_types=1);

namespace App\Doctrine\Type\Hr\Employee;

use App\Collection;

enum EnumNotificationType: string {
    case TYPE_DEFAULT = 'default';

    /** @var string */
    public const DEFAULT = 'default';

    /** @var string[] */
    public const ENUM = ['default'];

    /** @return string[] */
    public static function values(): array {
        return (new Collection(self::cases()))->map(static fn (self $case): string => $case->value)->toArray();
    }
}
