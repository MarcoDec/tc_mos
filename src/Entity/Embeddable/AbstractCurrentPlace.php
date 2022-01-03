<?php

namespace App\Entity\Embeddable;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Traits\NameTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass]
abstract class AbstractCurrentPlace {
    use NameTrait;

    #[
        ORM\Column(type: 'datetime', nullable: false),
        Serializer\Groups(['read:current_place', 'write:current_place'])
    ]
    protected ?DateTime $date;

    #[
        ApiProperty(description: 'Nom', required: true),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    public function __construct(?string $name = null) {
        $this->date = new DateTime();
        $this->name = $name;
    }

    final public function __toString(): string {
        return $this->name ?? '';
    }

    abstract public function getTrafficLight(): int;

    final public function getDate(): ?DateTime {
        return $this->date;
    }

    final public function setDate(?DateTime $date): self {
        $this->date = $date;
        return $this;
    }
}
