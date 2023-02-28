<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use App\Collection;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/** @template T */
abstract class SetType extends EnumType {
    /** @param T $type */
    abstract protected function convertToDatabaseValueEnum(mixed $type): string;

    /** @return T */
    abstract protected function convertToPHPValueEnum(string $type): mixed;

    /** @param Collection<int, T> $value */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): string {
        return $value->map(fn (mixed $type): string => $this->convertToDatabaseValueEnum($type))->implode(',');
    }

    /**
     * @param  string             $value
     * @return Collection<int, T>
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): Collection {
        return Collection::explode(',', $value)->map(fn (string $type): mixed => $this->convertToPHPValueEnum($type));
    }

    public function getEnumType(): string {
        return 'SET';
    }
}
