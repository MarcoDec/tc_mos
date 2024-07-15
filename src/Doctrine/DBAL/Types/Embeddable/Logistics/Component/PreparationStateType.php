<?php

namespace App\Doctrine\DBAL\Types\Embeddable\Logistics\Component;

use App\Doctrine\DBAL\Types\Embeddable\StateType;

final class PreparationStateType extends StateType {
    final public const TYPES = [
        self::TYPE_STATE_ASKED,
        self::TYPE_STATE_AGREED,
        self::TYPE_STATE_DELIVERED,
        self::TYPE_STATE_REJECTED
    ];

    public function getName(): string {
        return 'preparation_state';
    }
}