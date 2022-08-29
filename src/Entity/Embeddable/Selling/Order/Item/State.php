<?php

namespace App\Entity\Embeddable\Selling\Order\Item;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\Selling\Order\CustomerOrderItemStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class State extends AbstractState {
    final public const TRANSITIONS = [self::TR_DELIVER, self::TR_PARTIALLY_DELIVER, self::TR_VALIDATE];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => CustomerOrderItemStateType::TYPES]),
        ORM\Column(type: 'customer_order_item_state', options: ['default' => 'draft']),
        Serializer\Groups(['read:state'])
    ]
    protected string $state = CustomerOrderItemStateType::TYPE_STATE_DRAFT;
}
