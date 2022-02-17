<?php

namespace App\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type as DoctrineType;

abstract class Type extends DoctrineType {
    /** @var string[] */
    public const TYPES = [];

    private static function getStrTypes(): string {
        return collect(static::TYPES)
            ->map(static fn (string $type): string => "'$type'")
            ->implode(', ');
    }

    final public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed {
        if (!empty($value) && !in_array($value, static::TYPES, true)) {
            throw new InvalidArgumentException(sprintf("Invalid value. Get \"$value\", but valid values are [%s].", self::getStrTypes()));
        }
        return parent::convertToDatabaseValue($value, $platform);
    }

    final public function getSQLDeclaration(array $column, AbstractPlatform $platform): string {
        return sprintf('ENUM(%s)', self::getStrTypes());
    }
}
