<?php

namespace App\Doctrine\DBAL\Types\Production\Manufacturing\DeliveryNote;

use App\Doctrine\DBAL\Types\CurrentPlaceType as AbstractCurrentPlaceType;

final class CurrentPlaceType extends AbstractCurrentPlaceType {
    public const TYPES = [self::TYPE_DRAFT, self::TYPE_READY_TO_SENT, self::TYPE_SENT];

    public function getName(): string {
        return 'delivery_note_current_place';
    }
}
