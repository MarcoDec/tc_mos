<?php

namespace App\Entity\Embeddable\Production\Manufacturing\Expedition;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\Production\Manufacturing\ExpeditionStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class State extends AbstractState {
    final public const TRANSITIONS = [self::TR_SUBMIT_VALIDATION, self::TR_VALIDATE];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => ExpeditionStateType::TYPES]),
        ORM\Column(type: 'expedition_state', options: ['default' => 'draft']),
        Serializer\Groups(['read:state'])
    ]
    protected string $state = ExpeditionStateType::TYPE_STATE_DRAFT;
}
