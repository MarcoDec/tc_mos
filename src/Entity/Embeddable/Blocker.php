<?php

namespace App\Entity\Embeddable;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\BlockerStateType;
use App\Doctrine\DBAL\Types\Embeddable\StateType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class Blocker extends State {
    final public const TRANSITIONS = [self::TR_BLOCK, self::TR_DISABLE, self::TR_UNLOCK, self::TR_SUBROGATE];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => BlockerStateType::TYPES]),
        ORM\Column(type: 'blocker_state', options: ['default' => 'enabled']),
        Serializer\Groups(['read:state',  'read:operation-employee:collection'])
    ]
    protected string $state = StateType::TYPE_STATE_ENABLED;
}
