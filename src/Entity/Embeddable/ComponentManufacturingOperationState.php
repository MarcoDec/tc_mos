<?php

namespace App\Entity\Embeddable;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\ComponentManufacturingOperationStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class ComponentManufacturingOperationState extends AbstractState {
    final public const TRANSITIONS = [self::TR_SUPERVISE, self::TR_VALIDATE];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => ComponentManufacturingOperationStateType::TYPES]),
        ORM\Column(type: 'component_manufacturing_operation_state', options: ['default' => 'draft']),
        Serializer\Groups(['read:state'])
    ]
    protected string $state = ComponentManufacturingOperationStateType::TYPE_STATE_DRAFT;
}
