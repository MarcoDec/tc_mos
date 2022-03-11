<?php

namespace App\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\SimpleArrayType as DoctrineSimpleArrayType;

final class SimpleArrayType extends DoctrineSimpleArrayType {
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string {
        return 'TEXT';
    }
}
