<?php

namespace App\Doctrine\DBAL\Types\Embeddable\Production\Manufacturing;

use App\Doctrine\DBAL\Types\Embeddable\StateType;

final class DeliveryNoteStateType extends StateType {
    final public const TYPES = [self::TYPE_STATE_DRAFT, self::TYPE_STATE_SENT, self::TYPE_STATE_ACKNOWLEDGMENT_OF_RECEIPT ,self::TYPE_STATE_REJECTED];

    public function getName(): string {
        return 'delivery_note_state';
    }
}