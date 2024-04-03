<?php

namespace App\Entity\Embeddable\Production\Engine;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\Production\Engine\EngineStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class State extends AbstractState {
    final public const TRANSITIONS = [self::TR_SUPERVISE, self::TR_UNDER_MAINTENANCE, self::TR_VALIDATE];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => EngineStateType::TYPES]),
        ORM\Column(type: 'engine_state', options: ['default' => 'draft']),
        Serializer\Groups(['read:state'])
    ]
    protected string $state = EngineStateType::TYPE_STATE_DRAFT;
}