<?php

namespace App\Entity\Embeddable\Manufacturing\Operation;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\Manufacturing\OperationStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class State extends AbstractState {
    final public const TRANSITIONS = [self::TR_SUPERVISE, self::TR_VALIDATE, self::TR_LOCK];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => OperationStateType::TYPES]),
        ORM\Column(type: 'manufacturing_operation_state', options: ['default' => 'draft']),
        Serializer\Groups(['read:state'])
    ]
    protected string $state = OperationStateType::TYPE_STATE_DRAFT;
}
