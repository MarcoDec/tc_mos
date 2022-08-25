<?php

namespace App\Entity\Embeddable\Manufacturing\Order;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\Manufacturing\OrderStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class State extends AbstractState {
    final public const TRANSITIONS = [
        self::TR_ACCEPT,
        self::TR_BLOCK,
        self::TR_CLOSE,
        self::TR_REJECT,
        self::TR_UNLOCK
    ];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => OrderStateType::TYPES]),
        ORM\Column(type: 'manufacturing_order_state', options: ['default' => 'asked,enabled']),
        Serializer\Groups(['read:state'])
    ]
    protected array $state = [OrderStateType::TYPE_STATE_ASKED => 1, OrderStateType::TYPE_STATE_ENABLED => 1];
}
