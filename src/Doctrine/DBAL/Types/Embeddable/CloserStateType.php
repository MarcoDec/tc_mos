<?php

namespace App\Doctrine\DBAL\Types\Embeddable;

final class CloserStateType extends StateType {
    final public const TYPES = [self::TYPE_STATE_BLOCKED, self::TYPE_STATE_CLOSED, self::TYPE_STATE_ENABLED];

    public function getName(): string {
        return 'closer_state';
    }
}
