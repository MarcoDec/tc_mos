<?php

namespace App\Entity\Embeddable\Production\Manufacturing\DeliveryNote;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\Production\Manufacturing\DeliveryNoteStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class State extends AbstractState {
    final public const TRANSITIONS = [self::TR_ACCEPT, self::TR_VALIDATE, self::TR_REJECT];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => DeliveryNoteStateType::TYPES]),
        ORM\Column(type: 'delivery_note_state', options: ['default' => 'draft']),
        Serializer\Groups(['read:state'])
    ]
    protected string $state = DeliveryNoteStateType::TYPE_STATE_DRAFT;
}
