<?php

namespace App\Entity\Hr\Employee\Skill;

use App\Entity\Entity;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity,
    ORM\Table(name: 'skill_type'),
    UniqueEntity('name')
]
class Type extends Entity {
    #[
        Assert\Length(min: 3, max: 50),
        Assert\NotBlank,
        ORM\Column(length: 50)
    ]
    private ?string $name = null;

    final public function getName(): ?string {
        return $this->name;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }
}
