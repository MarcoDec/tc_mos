<?php

namespace App\Doctrine\DBAL\Types\Embeddable;

final class EventStateType extends StateType {
    final public const TYPES = [
        self::TYPE_STATE_AGREED,
        self::TYPE_STATE_ASKED,
        self::TYPE_STATE_CLOSED,
        self::TYPE_STATE_REJECTED
    ];

    public function getName(): string {
        return 'event_state';
    }
}
