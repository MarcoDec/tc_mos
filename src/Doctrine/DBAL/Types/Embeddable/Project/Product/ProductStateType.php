<?php

namespace App\Doctrine\DBAL\Types\Embeddable\Project\Product;

use App\Doctrine\DBAL\Types\Embeddable\StateType;

final class ProductStateType extends StateType {
    final public const TYPES = [
        self::TYPE_STATE_AGREED,
        self::TYPE_STATE_DRAFT,
        self::TYPE_STATE_TO_VALIDATE,
        self::TYPE_STATE_WARNING,
        self::TYPE_STATE_CLOSED
    ];

    public function getName(): string {
        return 'product_state';
    }
}
