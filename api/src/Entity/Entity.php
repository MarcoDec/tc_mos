<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\MappedSuperclass]
abstract class Entity {
    #[ORM\Column(options: ['default' => false])]
    private bool $deleted = false;

    #[
        ApiProperty(description: 'Id', example: 1),
        ORM\Column(options: ['unsigned' => true]),
        ORM\GeneratedValue,
        ORM\Id,
        Serializer\Groups('id')
    ]
    private ?int $id = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function isDeleted(): bool {
        return $this->deleted;
    }
}
