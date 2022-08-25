<?php

namespace App\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\DBAL\Platforms\AbstractPlatform;

abstract class SetType extends EnumType {
    /**
     * @param array<string, 1>|string[] $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string {
        if (empty($value)) {
            return null;
        }
        foreach ($value as $item) {
            if (!in_array($item, static::TYPES, true)) {
                throw new InvalidArgumentException(sprintf("Invalid value. Get \"$item\", but valid values are [%s].", static::getStrTypes()));
            }
        }
        return implode(',', $value);
    }

    /**
     * @param string $value
     *
     * @return array<string, 1>|string[]
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): array {
        return empty($value) ? [] : explode(',', $value);
    }

    final public function getEnumType(): string {
        return 'SET';
    }
}
