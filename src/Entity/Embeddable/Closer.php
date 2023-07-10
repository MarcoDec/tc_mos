<?php

namespace App\Entity\Embeddable;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\CloserStateType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class Closer extends State {
    final public const TRANSITIONS = [self::TR_BLOCK, self::TR_CLOSE, self::TR_UNLOCK];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => CloserStateType::TYPES]),
        ORM\Column(type: 'closer_state', options: ['default' => 'enabled']),
        Serializer\Groups(['read:state', 'read:operation-employee:collection'])
    ]
    protected string $state = CloserStateType::TYPE_STATE_ENABLED;
}
