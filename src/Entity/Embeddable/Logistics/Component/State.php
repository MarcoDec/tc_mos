<?php

namespace App\Entity\Embeddable\Logistics\Component;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\Logistics\Component\PreparationStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class State extends AbstractState {
    final public const TRANSITIONS = [self::TR_VALIDATE, self::TR_DELIVER, self::TR_REJECT];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => PreparationStateType::TYPES]),
        ORM\Column(type: 'preparation_state', options: ['default' => 'asked']),
        Serializer\Groups(['read:state'])
    ]
    protected string $state = PreparationStateType::TYPE_STATE_ASKED;
}
