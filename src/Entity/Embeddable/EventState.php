<?php

namespace App\Entity\Embeddable;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\EventStateType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class EventState extends State {
    final public const TRANSITIONS = [self::TR_SUPERVISE, self::TR_VALIDATE];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => EventStateType::TYPES]),
        ORM\Column(type: 'event_state', options: ['default' => 'asked']),
        Serializer\Groups(['read:state'])
    ]
    protected string $state = EventStateType::TYPE_STATE_ASKED;
}
