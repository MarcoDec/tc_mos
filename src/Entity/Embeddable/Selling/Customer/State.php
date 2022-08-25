<?php

namespace App\Entity\Embeddable\Selling\Customer;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\Selling\Customer\CustomerStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class State extends AbstractState {
    final public const TRANSITIONS = [
        self::TR_BLOCK,
        self::TR_DISABLE,
        self::TR_UNLOCK,
        self::TR_VALIDATE
    ];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => CustomerStateType::TYPES]),
        ORM\Column(type: 'customer_state', options: ['default' => 'draft,enabled']),
        Serializer\Groups(['read:state'])
    ]
    protected array $state = [CustomerStateType::TYPE_STATE_DRAFT => 1, CustomerStateType::TYPE_STATE_ENABLED => 1];
}
