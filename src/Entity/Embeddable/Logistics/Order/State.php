<?php

namespace App\Entity\Embeddable\Logistics\Order;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\Logistics\Order\ReceiptStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class State extends AbstractState {
    final public const TRANSITIONS = [self::TR_REJECT, self::TR_SUBMIT_VALIDATION, self::TR_VALIDATE];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => ReceiptStateType::TYPES]),
        ORM\Column(type: 'check_state', options: ['default' => 'asked']),
        Serializer\Groups(['read:state'])
    ]
    protected string $state = ReceiptStateType::TYPE_STATE_ASKED;
}
