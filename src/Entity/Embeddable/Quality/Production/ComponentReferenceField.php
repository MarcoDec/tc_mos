<?php

namespace App\Entity\Embeddable\Quality\Production;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Embeddable\Measure;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class ComponentReferenceField {
    #[
        ApiProperty(description: 'Requis', example: true),
        ORM\Column(options: ['default' => true]),
        Serializer\Groups(['read:component-reference-field', 'write:component-reference-field'])
    ]
    private bool $required = true;

    #[
        ApiProperty(description: 'Tolérance'),
        ORM\Embedded,
        Assert\PositiveOrZero,
        Serializer\Groups(['read:component-reference-field', 'write:component-reference-field'])
    ]
    private Measure $tolerance;

    #[
        ApiProperty(description: 'Valeur'),
        ORM\Embedded,
        Assert\PositiveOrZero,
        Serializer\Groups(['read:component-reference-field', 'write:component-reference-field'])
    ]
    private Measure $value;

    public function __construct() {
        $this->tolerance = new Measure();
        $this->value = new Measure();
    }

    final public function getMax(): Measure {
        return $this->value->add($this->tolerance);
    }

    final public function getMin(): Measure {
        $min = $this->value->substract($this->tolerance);
        return $min->getValue() >= 0
            ? $min
            : (new Measure())->setCode($this->value->getCode())->setUnit($this->value->getUnit())->setValue(0);
    }

    final public function getTolerance(): Measure {
        return $this->tolerance;
    }

    final public function getValue(): Measure {
        return $this->value;
    }

    final public function isRequired(): bool {
        return $this->required;
    }

    /**
     * TODO use Measure.
     */
    final public function isValid(float $value): bool {
        return !$this->required || $this->getMin()->getValue() <= $value && $this->getMax()->getValue() >= $value;
    }

    final public function setRequired(bool $required): self {
        $this->required = $required;
        return $this;
    }

    final public function setTolerance(Measure $tolerance): self {
        $this->tolerance = $tolerance;
        return $this;
    }

    final public function setValue(Measure $value): self {
        $this->value = $value;
        return $this;
    }
}
