<?php

namespace App\Entity\Embeddable\Purchase\Order\Item;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\Purchase\Order\ItemStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class State extends AbstractState {
    final public const TRANSITIONS = [
        self::TR_CREATE,
        self::TR_DELIVER,
        self::TR_FORECAST,
        self::TR_MONTH,
        self::TR_PARTIALLY_DELIVER,
        self::TR_VALIDATE,
        self::TR_LOCK
    ];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => ItemStateType::TYPES]),
        ORM\Column(type: 'purchase_order_item_state', options: ['default' => 'initial']),
        Serializer\Groups(['read:state'])
    ]
    protected string $state = ItemStateType::TYPE_STATE_INITIAL;
}
