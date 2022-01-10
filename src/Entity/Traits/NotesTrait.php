<?php

namespace App\Entity\Traits;

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

trait NotesTrait {
    #[
        ApiProperty(description: 'Notes', required: false),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:notes', 'write:notes'])
    ]
    protected ?string $notes = null;

    public function getNotes(): ?string {
        return $this->notes;
    }

    final public function setNotes(?string $notes): self {
        $this->notes = $notes;
        return $this;
    }
}
