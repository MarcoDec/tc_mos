<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\MappedSuperclass]
abstract class Entity {
    #[ORM\Column(options: ['default' => false])]
    private bool $deleted = false;

    #[
        ORM\Column(options: ['unsigned' => true]),
        ORM\GeneratedValue,
        ORM\Id
    ]
    private ?int $id = null;

    public function __clone() {
        $this->id = null;
    }

    #[
        ApiProperty(description: 'id', required: true, identifier: true, example: 1),
        Serializer\Groups(['read:id'])
    ]
    public function getId(): ?int {
        return $this->id;
    }
    #[
        ApiProperty(description: 'true when the item has been deleted', example: false),
        Serializer\Groups(['read:id'])
    ]
    final public function isDeleted(): bool {
        return $this->deleted;
    }
    final public function setDeleted(bool $deleted) {
        $this->deleted = $deleted;
    }
}
