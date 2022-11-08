<?php

declare(strict_types=1);

namespace App\Doctrine\Type\Hr\Employee;

use App\Collection;

enum Role: string {
    case MANAGEMENT_ADMIN = 'ROLE_MANAGEMENT_ADMIN';
    case PURCHASE_ADMIN = 'ROLE_PURCHASE_ADMIN';

    /** @var string[] */
    public const ENUM = [self::ROLE_MANAGEMENT_ADMIN, self::ROLE_PURCHASE_ADMIN];

    /** @var string */
    public const GRANTED_MANAGEMENT_ADMIN = 'is_granted(\''.self::ROLE_MANAGEMENT_ADMIN.'\')';

    /** @var string */
    public const GRANTED_PURCHASE_ADMIN = 'is_granted(\''.self::ROLE_PURCHASE_ADMIN.'\')';

    /** @var string */
    public const ROLE_MANAGEMENT_ADMIN = 'ROLE_MANAGEMENT_ADMIN';

    /** @var string */
    public const ROLE_PURCHASE_ADMIN = 'ROLE_PURCHASE_ADMIN';

    /** @return string[] */
    public static function values(): array {
        return (new Collection(self::cases()))->map(static fn (self $case): string => $case->value)->toArray();
    }
}
