<?php

namespace App\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\DBAL\Platforms\AbstractPlatform;

abstract class EnumType extends Type {
    /**
     * @noRector
     *
     * @var string[]
     */
    public const TYPES = [];

    private static function getStrTypes(): string {
        return collect(static::TYPES)
            ->map(static fn (string $type): string => "'$type'")
            ->implode(', ');
    }

    /**
     * @param string $value
     */
    final public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed {
        if (!empty($value) && !in_array($value, static::TYPES, true)) {
            throw new InvalidArgumentException(sprintf("Invalid value. Get \"$value\", but valid values are [%s].", self::getStrTypes()));
        }
        return parent::convertToDatabaseValue($value, $platform);
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string {
        return sprintf('ENUM(%s)', self::getStrTypes());
    }
}
