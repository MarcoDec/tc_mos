<?php

namespace App\Entity\Embeddable;

use App\Entity\Traits\NameTrait;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass]
abstract class AbstractCurrentPlace {
    use NameTrait;

     #[
        ApiProperty(description: 'Nom', required: true),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    #[
        ORM\Column(type: "datetime", nullable: false),
        Serializer\Groups(['read:current_place', 'write:current_place'])
    ]
    protected ?\DateTime $date;

    public function __construct(?string $name = null) {
        $this->date = new \DateTime();
        $this->name = $name;
    }

    // final public function setName(?string $name): self {
    //     $this->name = $name;
    //     return $this;
    // }

    final public function getDate(): ?\DateTime {
        return $this->date;
    }

    abstract public function getTrafficLight(): int;

    final public function setDate(?\DateTime $date): self {
        $this->date = $date;
        return $this;
    }

    // final public function getName(): ?string {
    //     return $this->name;
    // }

    final public function __toString(): string {
        return $this->name;
    }
}