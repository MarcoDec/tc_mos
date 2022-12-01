<?php

declare(strict_types=1);

namespace App\Doctrine\Type\Hr\Employee;

use App\Collection;

enum Role: string {
    case HR_ADMIN = 'ROLE_HR_ADMIN';
    case HR_READER = 'ROLE_HR_READER';
    case HR_WRITER = 'ROLE_HR_WRITER';
    case LOGISTICS_ADMIN = 'ROLE_LOGISTICS_ADMIN';
    case LOGISTICS_READER = 'ROLE_LOGISTICS_READER';
    case LOGISTICS_WRITER = 'ROLE_LOGISTICS_WRITER';
    case MANAGEMENT_ADMIN = 'ROLE_MANAGEMENT_ADMIN';
    case PRODUCTION_ADMIN = 'ROLE_PRODUCTION_ADMIN';
    case PRODUCTION_READER = 'ROLE_PRODUCTION_READER';
    case PRODUCTION_WRITER = 'ROLE_PRODUCTION_WRITER';
    case PROJECT_ADMIN = 'ROLE_PROJECT_ADMIN';
    case PURCHASE_ADMIN = 'ROLE_PURCHASE_ADMIN';
    case USER = 'ROLE_USER';

    /** @var string */
    public const GRANTED_HR_ADMIN = 'is_granted(\''.self::ROLE_HR_ADMIN.'\')';

    /** @var string */
    public const GRANTED_HR_READER = 'is_granted(\''.self::ROLE_HR_READER.'\')';

    /** @var string */
    public const GRANTED_HR_WRITER = 'is_granted(\''.self::ROLE_HR_WRITER.'\')';

    /** @var string */
    public const GRANTED_LOGISTICS_ADMIN = 'is_granted(\''.self::ROLE_LOGISTICS_ADMIN.'\')';

    /** @var string */
    public const GRANTED_LOGISTICS_READER = 'is_granted(\''.self::ROLE_LOGISTICS_READER.'\')';

    /** @var string */
    public const GRANTED_LOGISTICS_WRITER = 'is_granted(\''.self::ROLE_LOGISTICS_WRITER.'\')';

    /** @var string */
    public const GRANTED_MANAGEMENT_ADMIN = 'is_granted(\''.self::ROLE_MANAGEMENT_ADMIN.'\')';

    /** @var string */
    public const GRANTED_PRODUCTION_ADMIN = 'is_granted(\''.self::ROLE_PRODUCTION_ADMIN.'\')';

    /** @var string */
    public const GRANTED_PRODUCTION_READER = 'is_granted(\''.self::ROLE_PRODUCTION_READER.'\')';

    /** @var string */
    public const GRANTED_PRODUCTION_WRITER = 'is_granted(\''.self::ROLE_PRODUCTION_WRITER.'\')';

    /** @var string */
    public const GRANTED_PROJECT_ADMIN = 'is_granted(\''.self::ROLE_PROJECT_ADMIN.'\')';

    /** @var string */
    public const GRANTED_PURCHASE_ADMIN = 'is_granted(\''.self::ROLE_PURCHASE_ADMIN.'\')';

    /** @var array<string, string> */
    public const ROLE_HIERARCHY = [
        self::ROLE_HR_ADMIN => self::ROLE_HR_WRITER,
        self::ROLE_HR_READER => self::ROLE_USER,
        self::ROLE_HR_WRITER => self::ROLE_HR_READER,
        self::ROLE_LOGISTICS_ADMIN => self::ROLE_LOGISTICS_WRITER,
        self::ROLE_LOGISTICS_READER => self::ROLE_USER,
        self::ROLE_LOGISTICS_WRITER => self::ROLE_LOGISTICS_READER,
        self::ROLE_MANAGEMENT_ADMIN => self::ROLE_USER,
        self::ROLE_PROJECT_ADMIN => self::ROLE_USER,
        self::ROLE_PRODUCTION_ADMIN => self::ROLE_PRODUCTION_WRITER,
        self::ROLE_PRODUCTION_READER => self::ROLE_USER,
        self::ROLE_PRODUCTION_WRITER => self::ROLE_PRODUCTION_READER,
        self::ROLE_PURCHASE_ADMIN => self::ROLE_USER
    ];

    /** @var string */
    public const ROLE_HR_ADMIN = 'ROLE_HR_ADMIN';

    /** @var string */
    public const ROLE_HR_READER = 'ROLE_HR_READER';

    /** @var string */
    public const ROLE_HR_WRITER = 'ROLE_HR_WRITER';

    /** @var string */
    public const ROLE_LOGISTICS_ADMIN = 'ROLE_LOGISTICS_ADMIN';

    /** @var string */
    public const ROLE_LOGISTICS_READER = 'ROLE_LOGISTICS_READER';

    /** @var string */
    public const ROLE_LOGISTICS_WRITER = 'ROLE_LOGISTICS_WRITER';

    /** @var string */
    public const ROLE_MANAGEMENT_ADMIN = 'ROLE_MANAGEMENT_ADMIN';

    /** @var string */
    public const ROLE_PRODUCTION_ADMIN = 'ROLE_PRODUCTION_ADMIN';

    /** @var string */
    public const ROLE_PRODUCTION_READER = 'ROLE_PRODUCTION_READER';

    /** @var string */
    public const ROLE_PRODUCTION_WRITER = 'ROLE_PRODUCTION_WRITER';

    /** @var string */
    public const ROLE_PROJECT_ADMIN = 'ROLE_PROJECT_ADMIN';

    /** @var string */
    public const ROLE_PURCHASE_ADMIN = 'ROLE_PURCHASE_ADMIN';

    /** @var string */
    public const ROLE_USER = 'ROLE_USER';

    /** @return string[] */
    public static function values(): array {
        return (new Collection(self::cases()))->map(static fn (self $case): string => $case->value)->toArray();
    }
}
