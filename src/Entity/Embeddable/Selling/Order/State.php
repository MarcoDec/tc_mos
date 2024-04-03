<?php

namespace App\Entity\Embeddable\Selling\Order;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\Selling\Order\SellingOrderStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class State extends AbstractState {
    final public const TRANSITIONS = [
        self::TR_DELIVER,
        self::TR_PARTIALLY_DELIVER,
        self::TR_SUBMIT_VALIDATION,
        self::TR_VALIDATE,
        self::TR_PAY
    ];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => SellingOrderStateType::TYPES]),
        ORM\Column(type: 'selling_order_state', options: ['default' => 'draft']),
        Serializer\Groups(['read:state'])
    ]
    protected string $state = SellingOrderStateType::TYPE_STATE_DRAFT;
}
