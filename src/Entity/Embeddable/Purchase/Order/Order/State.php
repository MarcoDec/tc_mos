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
        self::TR_BLOCK,
        self::TR_BUY,
        self::TR_CLOSE,
        self::TR_CREATE,
        self::TR_DELIVER,
        self::TR_PARTIALLY_DELIVER,
        self::TR_UNLOCK,
        self::TR_VALIDATE
    ];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => OrderStateType::TYPES]),
        ORM\Column(type: 'supplier_order_state', options: ['default' => 'initial,enabled']),
        Serializer\Groups(['read:state'])
    ]
    protected array $state = [OrderStateType::TYPE_STATE_INITIAL => 1, OrderStateType::TYPE_STATE_ENABLED => 1];
}
