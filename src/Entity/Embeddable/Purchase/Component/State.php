<?php

namespace App\Entity\Embeddable\Purchase\Component;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\Purchase\Component\ComponentStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class State extends AbstractState {
    final public const TRANSITIONS = [self::TR_SUPERVISE, self::TR_VALIDATE, self::TR_CLOSE];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => ComponentStateType::TYPES]),
        ORM\Column(type: 'component_state', options: ['default' => 'draft']),
        Serializer\Groups(['read:state'])
    ]
    protected string $state = ComponentStateType::TYPE_STATE_DRAFT;
}
