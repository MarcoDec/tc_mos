<?php

namespace App\Doctrine\DBAL\Types\Logistics;

use App\Doctrine\DBAL\Types\EnumType;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\DBAL\Platforms\AbstractPlatform;

final class FamilyType extends EnumType {
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

    /**
     * @param string[] $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string {
        if (empty($value)) {
            return null;
        }
        foreach ($value as $item) {
            if (!in_array($item, self::TYPES, true)) {
                throw new InvalidArgumentException(sprintf("Invalid value. Get \"$item\", but valid values are [%s].", self::getStrTypes()));
            }
        }
        return implode(',', $value);
    }

    /**
     * @param string $value
     *
     * @return string[]
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): array {
        return empty($value) ? [] : explode(',', $value);
    }

    public function getEnumType(): string {
        return 'SET';
    }

    public function getName(): string {
        return 'warehouse_families';
    }
}
