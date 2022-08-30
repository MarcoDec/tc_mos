<?php

namespace App\Doctrine\DBAL\Types\Embeddable\Accounting;

use App\Doctrine\DBAL\Types\Embeddable\StateType;

final class BillStateType extends StateType {
    final public const TYPES = [
        self::TYPE_STATE_BILLED,
        self::TYPE_STATE_DRAFT,
        self::TYPE_STATE_PARTIALLY_PAID,
        self::TYPE_STATE_PAID
    ];

    public function getName(): string {
        return 'bill_state';
    }
}
