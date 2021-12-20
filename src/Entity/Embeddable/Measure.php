<?php

namespace App\Entity\Embeddable;

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
// use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Management\Unit;

#[ORM\Embeddable]
class Measure {
    #[
        ApiProperty(description: 'Code ', example: 'g'),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private ?string $code = null;

    #[
        ApiProperty(description: 'Valeur', example: '2.66'),
        ORM\Column(type: 'float', nullable: true),
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private ?float $value = 0;

    #[
        ApiProperty(description: 'Unité', example: '/api/units/5'),
        ORM\ManyToOne(targetEntity: Unit::class),
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private Unit $unit;

    final public function getCode(): ?string {
        return $this->code;
    }

    final public function getValue(): ?float {
        return $this->value;
    }

    final public function setCode(?string $code): self {
        $this->code = $code;

        return $this;
    }

    final public function setValue(?float $value): self {
        $this->value = $value;

        return $this;
    }

    final public function setUnit(Unit $unit): self {
        $this->unit = $unit;

        return $this;
    }

    final public function getParent(): ?self {
        return $this->parent;
    }

    // #[
    //     ApiProperty(description: 'Ajouter une unité', example: '/api/units/3'),
    //     Serializer\Groups(['read:measure'])
    // ]
    final public function add(Measure $measure) : self {
        // The measure's code is the same, then use a simple addition (g + g = g)
        if($this->equals($measure)) {
            $this->value += $measure->getValue();
        } else { // If the two codes ain't the same

            // check if we're talking about the same type of unit (e.g.: we can't add a kg to meters)
            if($this->unit->getTopCode() === $measure->unit->getTopCode()) {

                $originalUnit = $this->unit;

                // to avoid float rounding problems, use the smallest mesure (the Unit#base=0 one, so the topCode)
                $firstValue = $this->convert($this->unit->getTopUnit());
                $secondValue = $measure->convert($measure->unit->getTopUnit());

                // Apply the changes to the entity
                $this->value = $firstValue->getValue() + $secondValue->getValue();
                $this->unit = $this->unit->getTopUnit();
                $this->code == $this->unit->getCode();

                // Now convert it back to the wanted Unit
                $this->convert($originalUnit);

            } else { // this is not the same unit
                throw new \Exception('The two measures cannot be added as they are not of same type');
            }
        }

        return $this;
    }

    final public function substract(Measure $measure) : self {
        // The measure's code is the same, then use a simple substraction (g - g = g)
        if($this->equals($measure)) {
            $this->value -= $measure->getValue();
        } else { // If the two codes ain't the same

            // check if we're talking about the same type of unit (e.g.: we can't substract a kg to meters)
            if($this->unit->getTopCode() === $measure->unit->getTopCode()) {

                $originalUnit = $this->unit;

                // to avoid float rounding problems, use the smallest mesure (the Unit#base=0 one, so the topCode)
                $firstValue = $this->convert($this->unit->getTopUnit());
                $secondValue = $measure->convert($measure->unit->getTopUnit());

                // Apply the changes to the entity
                $this->value = $firstValue->getValue() - $secondValue->getValue();
                $this->unit = $this->unit->getTopUnit();
                $this->code == $this->unit->getCode();

                // Now convert it back to the wanted Unit
                $this->convert($originalUnit);

            } else { // this is not the same unit
                throw new \Exception('The two measures cannot be substracted as they are not of same type');
            }
        }

        return $this;
    }

    // Check if the measures have the same code
    final public function equals(Measure $measure) : bool {
        return $this->code == $measure->getCode();
    }

    // Converts a measure to the wanted unit
    final public function convert(Unit $unit) : self {

        // The unit to convert to isn't in the same family
        if($this->unit->getTopCode() !== $unit->getTopCode()) {
            throw new \Exception('The measure cannot be converted to another type of measure that is not in the same Unit tree');
        }

        // The unit to transform to is not the same
        if($this->unit !== $unit) {
            $this->value *= $this->unit->getMultiplicatorFromBase() / $unit->getMultiplicatorFromBase();
            $this->unit = $unit;
            $this->code = $unit->getCode();
        }

        return $this;
    }
}
