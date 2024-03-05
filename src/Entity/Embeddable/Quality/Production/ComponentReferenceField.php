<?php

namespace App\Entity\Embeddable\Quality\Production;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Embeddable\Measure;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Unit;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class ComponentReferenceField implements MeasuredInterface {
    #[
        ApiProperty(description: 'Requis', example: true),
        ORM\Column(options: ['default' => true]),
        Serializer\Groups(['read:component-reference-field', 'write:component-reference-field'])
    ]
    private bool $required = true;

    #[
        ApiProperty(description: 'Tolérance', openapiContext: ['$ref' => '#/components/schemas/Measure-length']),
        ORM\Embedded,
        Serializer\Groups(['read:component-reference-field', 'write:component-reference-field'])
    ]
    private Measure $tolerance;

    #[
        ApiProperty(description: 'Valeur', openapiContext: ['$ref' => '#/components/schemas/Measure-length']),
        ORM\Embedded,
        Serializer\Groups(['read:component-reference-field', 'write:component-reference-field'])
    ]
    private Measure $value;

    public function __construct() {
        $this->tolerance = new Measure();
        $this->value = new Measure();
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

    public function getMeasures(): array
    {
        return [$this->value, $this->tolerance];
    }
    public function getUnitMeasures(): array
    {
        return [$this->value, $this->tolerance];
    }
    public function getCurrencyMeasures(): array
    {
        return [];
    }

    public function getUnit(): ?Unit
    {
        return null;
    }
}
