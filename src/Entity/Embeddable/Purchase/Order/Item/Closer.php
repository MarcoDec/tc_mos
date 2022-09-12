<?php

namespace App\Entity\Embeddable\Purchase\Order\Item;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\Purchase\Order\CloserStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class Closer extends AbstractState {
    final public const TRANSITIONS = [self::TR_BLOCK, self::TR_CLOSE, self::TR_DELAY, self::TR_UNLOCK];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => CloserStateType::TYPES]),
        ORM\Column(type: 'purchase_order_item_closer_state', options: ['default' => 'enabled']),
        Serializer\Groups(['read:state'])
    ]
    protected string $state = CloserStateType::TYPE_STATE_ENABLED;
}
