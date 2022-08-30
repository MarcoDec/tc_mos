<?php

namespace App\Entity\Embeddable\Selling\Order;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\Selling\Order\CustomerOrderStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class State extends AbstractState {
    final public const TRANSITIONS = [
        self::TR_DELIVER,
        self::TR_PARTIALLY_DELIVER,
        self::TR_SUBMIT_VALIDATION,
        self::TR_VALIDATE
    ];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => CustomerOrderStateType::TYPES]),
        ORM\Column(type: 'customer_order_state', options: ['default' => 'draft']),
        Serializer\Groups(['read:state'])
    ]
    protected string $state = CustomerOrderStateType::TYPE_STATE_DRAFT;
}
