<?php

namespace App\Entity\Embeddable\Purchase\Supplier;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\Purchase\Supplier\SupplierStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class State extends AbstractState {
    final public const TRANSITIONS = [
        self::TR_BLOCK,
        self::TR_DISABLE,
        self::TR_SUBMIT_VALIDATION,
        self::TR_SUPERVISE,
        self::TR_UNLOCK,
        self::TR_VALIDATE
    ];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => SupplierStateType::TYPES]),
        ORM\Column(type: 'supplier_state', options: ['default' => 'draft,enabled']),
        Serializer\Groups(['read:state'])
    ]
    protected array $state = [SupplierStateType::TYPE_STATE_DRAFT => 1, SupplierStateType::TYPE_STATE_ENABLED => 1];
}
