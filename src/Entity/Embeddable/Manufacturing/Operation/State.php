<?php

namespace App\Entity\Embeddable\Manufacturing\Operation;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\Manufacturing\OperationStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class State extends AbstractState {
    final public const TRANSITIONS = [
        self::TR_BLOCK,
        self::TR_CLOSE,
        self::TR_SUPERVISE,
        self::TR_UNLOCK,
        self::TR_VALIDATE
    ];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => OperationStateType::TYPES]),
        ORM\Column(type: 'manufacturing_operation_state', options: ['default' => 'draft,enabled']),
        Serializer\Groups(['read:state'])
    ]
    protected array $state = [OperationStateType::TYPE_STATE_DRAFT => 1, OperationStateType::TYPE_STATE_ENABLED => 1];
}
