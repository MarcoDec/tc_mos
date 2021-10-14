<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\MappedSuperclass]
abstract class Entity {
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $deleted = false;

    #[
        ApiProperty(description: 'id', example: 1),
        ORM\Column(type: 'integer', options: ['unsigned' => true]),
        ORM\GeneratedValue,
        ORM\Id,
        Serializer\Groups(['read:id'])
    ]
    private int $id;

    final public function getId(): int {
        return $this->id;
    }

    final public function isDeleted(): bool {
        return $this->deleted;
    }
}
