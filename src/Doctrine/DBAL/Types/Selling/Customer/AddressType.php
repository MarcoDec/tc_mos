<?php

namespace App\Doctrine\DBAL\Types\Selling\Customer;

use App\Doctrine\DBAL\Types\EnumType;

final class AddressType extends EnumType {
    public const TYPE_BILLING = 'billing';
    public const TYPE_DELIVERY = 'delivery';
    public const TYPES = [self::TYPE_BILLING, self::TYPE_DELIVERY];

    public function getName(): string {
        return 'customer_address_type';
    }
}
