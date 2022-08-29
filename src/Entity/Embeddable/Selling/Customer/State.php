<?php

namespace App\Entity\Embeddable\Selling\Customer;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\Selling\Customer\CustomerStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class State extends AbstractState {
    final public const TRANSITIONS = [self::TR_VALIDATE];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => CustomerStateType::TYPES]),
        ORM\Column(type: 'customer_state', options: ['default' => 'draft']),
        Serializer\Groups(['read:state'])
    ]
    protected string $state = CustomerStateType::TYPE_STATE_DRAFT;
}
