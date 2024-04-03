<?php

namespace App\Doctrine\DBAL\Types\Embeddable\Production\Manufacturing;

use App\Doctrine\DBAL\Types\Embeddable\StateType;

final class ExpeditionStateType extends StateType {
    final public const TYPES = [self::TYPE_STATE_DRAFT, self::TYPE_STATE_READY_TO_SEND, self::TYPE_STATE_SENT];

    public function getName(): string {
        return 'expedition_state';
    }
}