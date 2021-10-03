<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class Entity {
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $deleted = false;
    #[
        ORM\Column(type: 'integer', options: ['unsigned' => true]),
        ORM\GeneratedValue,
        ORM\Id
    ]
    private int $id;

    final public function isDeleted(): bool {
        return $this->deleted;
    }

    final public function getId(): int {
        return $this->id;
    }
}
