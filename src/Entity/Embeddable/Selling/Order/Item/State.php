<?php

namespace App\Entity\Embeddable\Selling\Order\Item;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\Selling\Order\CustomerOrderItemStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class State extends AbstractState {
    final public const TRANSITIONS = [
        self::TR_BLOCK,
        self::TR_CLOSE,
        self::TR_DELIVER,
        self::TR_PARTIALLY_DELIVER,
        self::TR_UNLOCK,
        self::TR_VALIDATE
    ];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => CustomerOrderItemStateType::TYPES]),
        ORM\Column(type: 'customer_order_item_state', options: ['default' => 'draft,enabled']),
        Serializer\Groups(['read:state'])
    ]
    protected array $state = [CustomerOrderItemStateType::TYPE_STATE_DRAFT => 1, CustomerOrderItemStateType::TYPE_STATE_ENABLED => 1];
}
