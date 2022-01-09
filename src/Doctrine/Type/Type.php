<?php

namespace App\Doctrine\Type;

final class Type extends AbstractType {
    public const TYPE_ARRAY = 'ARRAY';
    public const TYPE_SELECT_MULTIPLE_LINK = 'SELECT_MULTIPLE_LINK';
    public const TYPE_INTEGER = 'INTEGER';
    public const TYPES = [self::TYPE_ARRAY, self::TYPE_SELECT_MULTIPLE_LINK, self::TYPE_INTEGER];

    public function getName(): string {
        return 'type';
    }
}
