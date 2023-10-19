<?php

namespace App\Doctrine\DBAL\Types\Embeddable\Purchase\Supplier;

use App\Doctrine\DBAL\Types\Embeddable\StateType;

final class SupplierStateType extends StateType {
    final public const TYPES = [
        self::TYPE_STATE_AGREED,
        self::TYPE_STATE_DRAFT,
        self::TYPE_STATE_TO_VALIDATE,
        self::TYPE_STATE_WARNING,
        self::TYPE_STATE_CLOSED
    ];

    public function getName(): string {
        return 'supplier_state';
    }
}
