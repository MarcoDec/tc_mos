<?php

namespace App\Doctrine\DBAL\Types\Embeddable;

use App\Doctrine\DBAL\Types\EnumType;

final class CopperType extends EnumType {
    final public const TYPE_DELIVERY = 'à la livraison';
    final public const TYPE_MONTHLY = 'mensuel';
    final public const TYPE_SEMI_ANNUAL = 'semestriel';
    final public const TYPES = [self::TYPE_DELIVERY, self::TYPE_MONTHLY, self::TYPE_SEMI_ANNUAL];

    public function getName(): string {
        return 'copper_type';
    }
}
