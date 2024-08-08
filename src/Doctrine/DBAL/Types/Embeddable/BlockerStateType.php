<?php

namespace App\Doctrine\DBAL\Types\Embeddable;

final class BlockerStateType extends StateType {
    final public const TYPES = [self::TYPE_STATE_BLOCKED, self::TYPE_STATE_DISABLED, self::TYPE_STATE_ENABLED, self::TYPE_STATE_UNDER_WAIVER];

    public function getName(): string {
        return 'blocker_state';
    }
}
