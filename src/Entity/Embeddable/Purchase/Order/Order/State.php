<?php

namespace App\Entity\Embeddable\Purchase\Order\Order;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\Purchase\Order\OrderStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class State extends AbstractState {
    final public const TRANSITIONS = [
        self::TR_BUY,
        self::TR_CREATE,
        self::TR_DELIVER,
        self::TR_PARTIALLY_DELIVER,
        self::TR_VALIDATE
    ];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => OrderStateType::TYPES]),
        ORM\Column(type: 'purchase_order_state', options: ['default' => 'initial']),
        Serializer\Groups(['read:state'])
    ]
    protected string $state = OrderStateType::TYPE_STATE_INITIAL;
}
