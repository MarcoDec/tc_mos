<?php

namespace App\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\TextType as DoctrineTextType;

final class TextType extends DoctrineTextType {
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string {
        return 'TEXT';
    }
}
