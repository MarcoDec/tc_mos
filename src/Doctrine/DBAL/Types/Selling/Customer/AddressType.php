<?php

namespace App\Doctrine\DBAL\Types\Selling\Customer;

use App\Doctrine\DBAL\Types\EnumType;

final class AddressType extends EnumType {
    final public const TYPE_BILLING = 'billing';
    final public const TYPE_DELIVERY = 'delivery';
    final public const TYPES = [self::TYPE_BILLING, self::TYPE_DELIVERY];

    public function getName(): string {
        return 'customer_address';
    }
}
