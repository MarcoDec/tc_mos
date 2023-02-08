<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;

class Tinyint extends Type {
    /** @var string */
    private const DECLARATION = 'TINYINT';

    public function getName(): string {
        return 'tinyint';
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string {
        return isset($column['unsigned']) && $column['unsigned'] ? self::DECLARATION.' UNSIGNED' : self::DECLARATION;
    }
}
