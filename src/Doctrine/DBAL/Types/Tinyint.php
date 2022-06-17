<?php

namespace App\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;

final class Tinyint extends Type {
    public function getName(): string {
        return 'tinyint';
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string {
        $declaration = 'TINYINT';
        return isset($column['unsigned']) && $column['unsigned'] ? "$declaration UNSIGNED" : $declaration;
    }
}
