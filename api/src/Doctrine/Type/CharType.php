<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;

class CharType extends Type {
    public function getName(): string {
        return 'char';
    }

    /** @param array{length: int} $column */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string {
        return "CHAR({$column['length']})";
    }
}
