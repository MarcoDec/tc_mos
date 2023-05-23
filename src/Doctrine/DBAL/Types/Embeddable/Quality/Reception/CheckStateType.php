<?php

namespace App\Doctrine\DBAL\Types\Embeddable\Quality\Reception;

use App\Doctrine\DBAL\Types\Embeddable\StateType;

final class CheckStateType extends StateType {
    final public const TYPES = [self::TYPE_STATE_ASKED, self::TYPE_STATE_BLOCKED, self::TYPE_STATE_CLOSED];

    public function getName(): string {
        return 'check_state';
    }
}
