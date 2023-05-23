<?php

declare(strict_types=1);

namespace App\Doctrine\Type\Purchase\Component;

use App\Doctrine\Type\EnumType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class AttributeType extends EnumType {
    /** @param EnumAttributeType $value */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string {
        return $value->value;
    }

    /** @param null|string $value */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?EnumAttributeType {
        return $value === null ? $value : EnumAttributeType::from($value);
    }

    public function getName(): string {
        return 'attribute';
    }

    /** @return string[] */
    protected function getEnumValues(): array {
        return EnumAttributeType::values();
    }
}
