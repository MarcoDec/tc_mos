<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use App\Collection;
use Doctrine\DBAL\Platforms\AbstractPlatform;

abstract class EnumType extends Type {
    /** @return string[] */
    abstract protected function getEnumValues(): array;

    public function getEnumType(): string {
        return 'ENUM';
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string {
        return sprintf("{$this->getEnumType()}(%s)", $this->getStrEnumValues());
    }

    private function getStrEnumValues(): string {
        return (new Collection($this->getEnumValues()))
            ->map(static fn (string $type): string => "'$type'")
            ->implode(',');
    }
}
