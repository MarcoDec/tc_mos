<?php

namespace App\Entity\Embeddable\Accounting;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\Accounting\BillStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class State extends AbstractState {
    final public const TRANSITIONS = [
        self::TR_BILL,
        self::TR_BLOCK,
        self::TR_DISABLE,
        self::TR_PARTIALLY_PAY,
        self::TR_PAY,
        self::TR_UNLOCK
    ];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => BillStateType::TYPES]),
        ORM\Column(type: 'bill_state', options: ['default' => 'draft,enabled']),
        Serializer\Groups(['read:state'])
    ]
    protected array $state = [BillStateType::TYPE_STATE_DRAFT => 1, BillStateType::TYPE_STATE_ENABLED => 1];
}
