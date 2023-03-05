<?php

namespace App\Doctrine\DBAL\Types\Logistics;

use App\Doctrine\DBAL\Types\SetType;

final class FamilyType extends SetType {
    final public const TYPE_JAIL = 'prison';
    final public const TYPE_PRODUCTION = 'production';
    final public const TYPE_RECEIPT = 'réception';
    final public const TYPE_SELL = 'magasin pièces finies';
    final public const TYPE_SHIPMENT = 'expédition';
    final public const TYPE_STORE = 'magasin matières premières';
    final public const TYPE_TRUCK = 'camion';
    final public const TYPES = [
        self::TYPE_JAIL,
        self::TYPE_PRODUCTION,
        self::TYPE_RECEIPT,
        self::TYPE_SELL,
        self::TYPE_SHIPMENT,
        self::TYPE_STORE,
        self::TYPE_TRUCK
    ];

    public function getName(): string {
        return 'warehouse_families';
    }
}
