<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class Entity {
    #[
        ORM\Column(options: ['unsigned' => true]),
        ORM\GeneratedValue,
        ORM\Id
    ]
    protected ?int $id = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $deleted = false;

    public function __clone() {
        $this->id = null;
    }

    final public function getId(): ?int {
        return $this->id;
    }

    final public function isDeleted(): bool {
        return $this->deleted;
    }
}
