<?php

namespace App\Doctrine\DBAL\Types;

final class ItemType extends EnumType {
    public const TYPE_COMPONENT = 'component';
    public const TYPE_PRODUCT = 'product';
    public const TYPES = [self::TYPE_COMPONENT, self::TYPE_PRODUCT];

    public function getName(): string {
        return 'item_type';
    }
}
