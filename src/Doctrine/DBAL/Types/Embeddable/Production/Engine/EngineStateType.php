<?php

namespace App\Doctrine\DBAL\Types\Embeddable\Production\Engine;

use App\Doctrine\DBAL\Types\Embeddable\StateType;

final class EngineStateType extends StateType {
    final public const TYPES = [self::TYPE_STATE_AGREED, self::TYPE_STATE_WARNING, self::TYPE_STATE_UNDER_MAINTENANCE];

    public function getName(): string {
        return 'engine_state';
    }
}
