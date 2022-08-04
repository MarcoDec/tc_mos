<?php

namespace App\Doctrine\DBAL\Types\Logistics;

use App\Doctrine\DBAL\Types\EnumType;

final class StockType extends EnumType {
    public const TYPE_COMPONENT = 'component';
    public const TYPE_PRODUCT = 'product';
    public const TYPES = [self::TYPE_COMPONENT, self::TYPE_PRODUCT];

    public function getName(): string {
        return 'stock_type';
    }
}
