<?php

namespace App\Doctrine\DBAL\Types\Embeddable\Purchase\Component;

use App\Doctrine\DBAL\Types\Embeddable\StateType;

class ComponentStateType extends StateType {
    final public const TYPES = [self::TYPE_STATE_AGREED, self::TYPE_STATE_DRAFT, self::TYPE_STATE_WARNING];

    public function getName(): string {
        return 'component_state';
    }
}
