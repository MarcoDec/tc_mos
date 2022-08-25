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

    protected static function getStrTypes(): string {
        return collect(static::TYPES)
            ->map(static fn (string $type): string => "'$type'")
            ->implode(', ');
    }

    /**
     * @param string|string[] $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed {
        if (
            !empty($value)
            && is_string($value)
            && !in_array($value, static::TYPES, true)
        ) {
            throw new InvalidArgumentException(sprintf("Invalid value. Get \"$value\", but valid values are [%s].", self::getStrTypes()));
        }
        return parent::convertToDatabaseValue($value, $platform);
    }

    public function getEnumType(): string {
        return 'ENUM';
    }

    final public function getSQLDeclaration(array $column, AbstractPlatform $platform): string {
        return sprintf("{$this->getEnumType()}(%s)", self::getStrTypes());
    }
}
