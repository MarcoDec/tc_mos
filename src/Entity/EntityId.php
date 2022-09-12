<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Serializer\Annotation as Serializer;

abstract class EntityId extends Entity {
    #[
        ApiProperty(description: 'id', required: true, identifier: true, example: 1),
        Serializer\Groups(['read:id'])
    ]
    public function getId(): ?int {
        /** @phpstan-ignore-next-line */
        return parent::getId();
    }
}
