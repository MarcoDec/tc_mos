<?php

namespace App\Doctrine\DBAL\Types\Quality\Reception\Check;

use App\Doctrine\DBAL\Types\EnumType;

final class CheckType extends EnumType {
    final public const TYPE_COMPANY = 'company';
    final public const TYPE_COMPONENT = 'component';
    final public const TYPE_COMPONENT_FAMILY = 'component_family';
    final public const TYPE_PRODUCT = 'product';
    final public const TYPE_PRODUCT_FAMILY = 'product_family';
    final public const TYPE_SUPPLIER = 'supplier';
    final public const TYPES = [
        self::TYPE_COMPONENT,
        self::TYPE_COMPONENT_FAMILY,
        self::TYPE_PRODUCT,
        self::TYPE_PRODUCT_FAMILY,
        self::TYPE_SUPPLIER
    ];

    public function getName(): string {
        return 'check';
    }
}
