<?php

namespace App\Entity\Hr\Event;

use App\Doctrine\DBAL\Types\Hr\Employee\CurrentPlaceType;
use App\Entity\Entity;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity,
    ORM\Table(name: 'event_type'),
    UniqueEntity('name')
]
class Type extends Entity {
    #[
        Assert\Length(min: 3, max: 30),
        Assert\NotBlank,
        ORM\Column(length: 30)
    ]
    private ?string $name = null;

    #[
        Assert\Choice(choices: CurrentPlaceType::TYPES),
        ORM\Column(type: 'employee_current_place', nullable: true, options: ['charset' => 'ascii'])
    ]
    private ?string $toStatus = null;

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getToStatus(): ?string {
        return $this->toStatus;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setToStatus(?string $toStatus): self {
        $this->toStatus = $toStatus;
        return $this;
    }
}
