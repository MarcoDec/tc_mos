<?php

namespace App\Doctrine\DBAL\Types\Logistics\Warehouse;

use App\Doctrine\DBAL\Types\Type;

final class WarehouseFamilyType extends Type {
    public const JAIL = 'prison';
    public const PRODUCTION = 'production';
    public const RECEIPT = 'réception';
    public const SELL = 'magasin pièces finies';
    public const SHIPMENT = 'expédition';
    public const STORE = 'magasin matières premières';
    public const TRUCK = 'camion';
    public const TYPES = [
        self::JAIL => self::JAIL,
        self::PRODUCTION => self::PRODUCTION,
        self::RECEIPT => self::RECEIPT,
        self::SELL => self::SELL,
        self::SHIPMENT => self::SHIPMENT,
        self::STORE => self::STORE,
        self::TRUCK => self::TRUCK,
    ];

    public function getName(): string {
        return 'warehouse_families';
    }
}
