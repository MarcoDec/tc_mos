<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\MappedSuperclass]
abstract class Entity {
    #[
        ApiProperty(description: 'id', identifier: true, example: 1),
        ORM\Column(options: ['unsigned' => true]),
        ORM\GeneratedValue,
        ORM\Id,
        Serializer\Groups(['read:id'])
    ]
    protected ?int $id = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $deleted = false;

    final public function getId(): ?int {
        return $this->id;
    }

    final public function isDeleted(): bool {
        return $this->deleted;
    }
}