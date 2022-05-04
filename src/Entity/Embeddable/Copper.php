<?php

namespace App\Entity\Embeddable;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\CopperType;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class Copper {
    #[
        ApiProperty(description: 'Indice du cuivre', openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:copper', 'write:copper'])
    ]
    private Measure $index;

    #[
        ApiProperty(description: 'Date du dernier indice'),
        Assert\DateTime,
        ORM\Column(type: 'datetime_immutable', nullable: true),
        Serializer\Groups(['read:copper', 'write:copper'])
    ]
    private ?DateTimeImmutable $last = null;

    #[
        ApiProperty(description: 'Activer le suivi du cuivre'),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:copper', 'write:copper'])
    ]
    private bool $managed = false;

    #[
        ApiProperty(description: 'Date du prochain indice'),
        Assert\DateTime,
        ORM\Column(type: 'datetime_immutable', nullable: true),
        Serializer\Groups(['read:copper', 'write:copper'])
    ]
    private ?DateTimeImmutable $next = null;

    #[
        ApiProperty(description: 'Type de suivi', example: CopperType::TYPE_MONTHLY, openapiContext: ['enum' => CopperType::TYPES]),
        Assert\Choice(choices: CopperType::TYPES),
        ORM\Column(type: 'copper_type', options: ['default' => CopperType::TYPE_MONTHLY]),
        Serializer\Groups(['read:copper', 'write:copper'])
    ]
    private string $type = CopperType::TYPE_MONTHLY;

    public function __construct() {
        $this->index = new Measure();
    }

    final public function getIndex(): Measure {
        return $this->index;
    }

    final public function getLast(): ?DateTimeImmutable {
        return $this->last;
    }

    final public function getNext(): ?DateTimeImmutable {
        return $this->next;
    }

    final public function getType(): string {
        return $this->type;
    }

    final public function isManaged(): bool {
        return $this->managed;
    }

    final public function setIndex(Measure $index): self {
        $this->index = $index;
        return $this;
    }

    final public function setLast(?DateTimeImmutable $last): self {
        $this->last = $last;
        return $this;
    }

    final public function setManaged(bool $managed): self {
        $this->managed = $managed;
        return $this;
    }

    final public function setNext(?DateTimeImmutable $next): self {
        $this->next = $next;
        return $this;
    }

    final public function setType(string $type): self {
        $this->type = $type;
        return $this;
    }
}
