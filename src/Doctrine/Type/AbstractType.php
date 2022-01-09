<?php

namespace App\Doctrine\Type;

use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

abstract class AbstractType extends Type {
    public const TYPES = [];
    protected const SQL = 'ENUM';

    final protected static function getStrTypes(): string {
        return collect(static::TYPES)->map(static fn (string $type): string => "'$type'")->implode(', ');
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        if (!empty($value) && !in_array($value, static::TYPES, true)) {
            throw new InvalidArgumentException(sprintf("Invalid value. Get \"$value\", but valid values are [%s].", static::getStrTypes()));
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    final public function getSQLDeclaration(array $column, AbstractPlatform $platform): string {
        return sprintf('%s(%s)', static::SQL, static::getStrTypes());
    }
}
