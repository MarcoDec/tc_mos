<?php

namespace App\Doctrine\Type;

final class Type extends AbstractType {
    public const TYPE_ARRAY = 'ARRAY';
    public const TYPE_SELECT_MULTIPLE_LINK = 'SELECT_MULTIPLE_LINK';
    public const TYPES = [self::TYPE_ARRAY, self::TYPE_SELECT_MULTIPLE_LINK];

    public function getName(): string {
        return 'type';
    }
}
