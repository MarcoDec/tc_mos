<?php

namespace App\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;

final class CharType extends Type {
    public function getName(): string {
        return 'char';
    }

    /**
     * @param array{length: int} $column
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string {
//        print_r($column);
//        echo $column['type'].'\n';
        if (isset($column['length'])) {
            return "CHAR({$column['length']})";
        }
        return $column['type'];
    }
}
