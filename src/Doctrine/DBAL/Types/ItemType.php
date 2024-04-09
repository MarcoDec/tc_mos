<?php

namespace App\Doctrine\DBAL\Types;

final class ItemType extends EnumType {
    final public const TYPE_COMPONENT = 'component';
    final public const TYPE_PRODUCT = 'product';
    final public const TYPES = [self::TYPE_COMPONENT, self::TYPE_PRODUCT];

    public function getName(): string {
        return 'item';
    }
}
