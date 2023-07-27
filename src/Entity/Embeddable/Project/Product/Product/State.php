<?php

namespace App\Entity\Embeddable\Project\Product\Product;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\Project\Product\ProductStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class State extends AbstractState {
    final public const TRANSITIONS = [self::TR_SUBMIT_VALIDATION, self::TR_SUPERVISE, self::TR_VALIDATE];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => ProductStateType::TYPES]),
        ORM\Column(type: 'product_state', options: ['default' => 'draft']),
        Serializer\Groups(['read:state', 'read:nomenclature'])
    ]
    protected string $state = ProductStateType::TYPE_STATE_DRAFT;
}
