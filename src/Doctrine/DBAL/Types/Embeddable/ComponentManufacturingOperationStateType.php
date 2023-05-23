<?php

namespace App\Doctrine\DBAL\Types\Embeddable;

class ComponentManufacturingOperationStateType extends StateType {
    final public const TYPES = [self::TYPE_STATE_AGREED, self::TYPE_STATE_DRAFT, self::TYPE_STATE_WARNING];

    public function getName(): string {
        return 'component_manufacturing_operation_state';
    }
}
