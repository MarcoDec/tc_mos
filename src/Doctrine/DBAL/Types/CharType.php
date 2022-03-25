<?php

namespace App\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class CharType extends Type {
    public function getName(): string {
        return 'char';
    }

    /**
     * @param array{length: int} $column
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string {
        return "CHAR({$column['length']})";
    }
}
