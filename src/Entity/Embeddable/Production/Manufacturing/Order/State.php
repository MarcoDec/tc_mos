<?php

namespace App\Entity\Embeddable\Production\Manufacturing\Order;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\Production\Manufacturing\OrderStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class State extends AbstractState {
    final public const TRANSITIONS = [self::TR_ACCEPT, self::TR_REJECT,self::TR_LOCK];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => OrderStateType::TYPES]),
        ORM\Column(type: 'manufacturing_order_state', options: ['default' => 'asked']),
        Serializer\Groups(['read:state'])
    ]
    protected string $state = OrderStateType::TYPE_STATE_ASKED;
}
