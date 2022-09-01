<?php

namespace App\Entity\Embeddable\Quality\Reception;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\Quality\Reception\CheckStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class State extends AbstractState {
    final public const TRANSITIONS = [self::TR_REJECT, self::TR_VALIDATE];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => CheckStateType::TYPES]),
        ORM\Column(type: 'receipt_state', options: ['default' => 'asked']),
        Serializer\Groups(['read:state'])
    ]
    protected string $state = CheckStateType::TYPE_STATE_ASKED;
}
