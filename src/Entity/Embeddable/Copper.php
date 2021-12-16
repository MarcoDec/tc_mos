<?php

namespace App\Entity\Embeddable;

use ApiPlatform\Core\Annotation\ApiProperty;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class Copper {
    public const COPPER_TYPE_DELIVERY = 'à la livraison';
    public const COPPER_TYPE_MONTHLY = 'mensuel';
    public const COPPER_TYPE_SEMI_ANNUAL = 'semestriel';
    public const COPPER_TYPES = [
        self::COPPER_TYPE_DELIVERY,
        self::COPPER_TYPE_MONTHLY,
        self::COPPER_TYPE_SEMI_ANNUAL,
    ];

    #[
        ApiProperty(description: 'Indice du cuivre', example: '5.3152'),
        Assert\PositiveOrZero,
        ORM\Column(options: ['default' => 10], type: 'float'),
        Serializer\Groups(['read:copper', 'write:copper'])
    ]
    private float $index = 10;

    #[
        ApiProperty(description: 'Date du dernier indice', example: '2020-10-31 11:45:59'),
        Assert\DateTime,
        ORM\Column(type: 'datetime', nullable: true),
        Serializer\Groups(['read:copper', 'write:copper'])
    ]
    private ?DateTimeInterface $last;

    #[
        ApiProperty(description: 'Activer le suivi du cuivre'),
        ORM\Column(options: ['default' => false], type: 'boolean'),
        Serializer\Groups(['read:copper', 'write:copper'])
    ]
    private bool $managed = false;

    #[
        ApiProperty(description: 'Date du prochain indice', example: '2020-10-31 11:46:38'),
        Assert\DateTime,
        ORM\Column(type: 'datetime', nullable: true),
        Serializer\Groups(['read:copper', 'write:copper'])
    ]
    private ?DateTimeInterface $next;

    #[
        ApiProperty(description: 'Type de suivi', example: self::COPPER_TYPE_MONTHLY),
        Assert\Choice(choices: self::COPPER_TYPES),
        ORM\Column(options: ['default' => self::COPPER_TYPE_MONTHLY], type: 'string'),
        Serializer\Groups(['read:copper', 'write:copper'])
    ]
    private string $type = self::COPPER_TYPE_MONTHLY;

    public function getIndex(): ?float {
        return $this->index;
    }

    public function getLast(): ?DateTimeInterface {
        return $this->last;
    }

    public function getManaged(): ?bool {
        return $this->managed;
    }

    public function getNext(): ?DateTimeInterface {
        return $this->next;
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function setIndex(float $index): self {
        $this->index = $index;

        return $this;
    }

    public function setLast(?DateTimeInterface $last): self {
        $this->last = $last;

        return $this;
    }

    public function setManaged(bool $managed): self {
        $this->managed = $managed;

        return $this;
    }

    public function setNext(?DateTimeInterface $next): self {
        $this->next = $next;

        return $this;
    }

    public function setType(string $type): self {
        $this->type = $type;

        return $this;
    }
}
