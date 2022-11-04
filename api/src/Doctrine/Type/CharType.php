<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class CharType extends Type {
    public function getName(): string {
        return 'char';
    }

    /** @param array{length: int} $column */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string {
        return "CHAR({$column['length']})";
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool {
        return true;
    }
}
