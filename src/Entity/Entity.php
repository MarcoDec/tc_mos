<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class Entity {
    #[
        ApiProperty(identifier: true),
        ORM\Column(type: 'integer', options: ['unsigned' => true]),
        ORM\GeneratedValue,
        ORM\Id
    ]
    protected int $id;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $deleted = false;

    final public function getId(): int {
        return $this->id;
    }

    final public function isDeleted(): bool {
        return $this->deleted;
    }
}
