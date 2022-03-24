<?php

namespace App\Entity;

use App\ApiPlatform\Core\Annotation\ApiProperty;
use App\ApiPlatform\Core\Annotation\ApiSerializerGroups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiSerializerGroups(inheritedRead: ['Resource' => ['Entity']]),
    ORM\MappedSuperclass
]
abstract class Entity {
    #[
        ApiProperty(readOnly: true, required: true),
        ORM\Column(options: ['unsigned' => true]),
        ORM\GeneratedValue,
        ORM\Id,
        Serializer\Groups(groups: ['Entity'])
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
