<?php

namespace App\Entity\Embeddable\Quality\Production;

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class ComponentReferenceField {
    #[
        ApiProperty(description: 'Requis', example: false),
        ORM\Column(options: ['default' => false], type: 'boolean'),
        Serializer\Groups(['read:component-reference-field', 'write:component-reference-field'])
    ]
    private bool $required = false;

    #[
        ApiProperty(description: 'Tolérance', example: 0),
        ORM\Column(options: ['default' => 0, 'unsigned' => true], type: 'float'),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:component-reference-field', 'write:component-reference-field'])
    ]
    private float $tolerance = 0;

    #[
        ApiProperty(description: 'Valeur', example: 0),
        ORM\Column(options: ['default' => 0, 'unsigned' => true], type: 'float'),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:component-reference-field', 'write:component-reference-field'])
    ]
    private float $value = 0;

    final public function getMax(): float {
        return $this->value + $this->tolerance;
    }

    final public function getMin(): float {
        $min = $this->value - $this->tolerance;
        return $min >= 0 ? $min : 0;
    }

    final public function getTolerance(): float {
        return $this->tolerance;
    }

    final public function getValue(): float {
        return $this->value;
    }

    final public function isRequired(): bool {
        return $this->required;
    }

    final public function isValid(float $value): bool {
        return !$this->required || $this->getMin() <= $value && $this->getMax() >= $value;
    }

    final public function setRequired(bool $required): self {
        $this->required = $required;
        return $this;
    }

    final public function setTolerance(float $tolerance): self {
        $this->tolerance = $tolerance;
        return $this;
    }

    final public function setValue(float $value): self {
        $this->value = $value;
        return $this;
    }
}
