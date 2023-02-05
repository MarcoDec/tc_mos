<?php

namespace App\Entity\Embeddable;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\BlockerStateType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class Blocker extends State {
    final public const TRANSITIONS = [self::TR_BLOCK, self::TR_DISABLE, self::TR_UNLOCK];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => BlockerStateType::TYPES]),
        ORM\Column(type: 'blocker_state', options: ['default' => 'enabled']),
        Serializer\Groups(['read:state'])
    ]
    protected string $state = BlockerStateType::TYPE_STATE_ENABLED;
}