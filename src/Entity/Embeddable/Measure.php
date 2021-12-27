<?php

namespace App\Entity\Embeddable;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Management\Unit;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class Measure {
    #[
        ApiProperty(description: 'Code ', example: 'g'),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private ?string $code = null;

    #[
        ApiProperty(description: 'Dénominateur', readableLink: false, example: '/api/units/3'),
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private ?Unit $denominator = null;

    #[
        ApiProperty(description: 'Unité', readableLink: false, example: '/api/units/2'),
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private Unit $unit;

    #[
        ApiProperty(description: 'Valeur', example: '2.66'),
        ORM\Column(type: 'float', nullable: true),
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private ?float $value = 0;

    final public function add(self $measure): self {
        // The measure's code is the same, then use a simple addition (g + g = g)
        if ($this->equals($measure)) {
            $this->value += $measure->getValue();
        } else { // If the two codes ain't the same

            // We have a composed unit
            if (null !== $this->denominator) {

                // check if there is a denominator to the second measure too
                if (null !== $measure->getDenominator()) {
                    // Check if both measure are from the same family tree
                    if (
                        $this->unit->getTopUnit()->getCode() === $measure->getUnit()->getTopUnit()->getCode()
                        && $this->denominator->getTopUnit()->getCode() === $measure->getDenominator()->getTopUnit()->getCode()
                    ) {
                        // Let's convert and do the maths
                        $originalUnit = $this->unit;
                        $originalDenominator = $this->denominator;

                        // First step: convert the unit to the smallest value (unit#base = 0)
                        // e.g. : 50 kg/km ==> 50 000 g / km
                        $unitValue = (new self())
                            ->setValue($this->value)
                            ->setUnit($this->unit);
                        $unitValue->convert($this->unit->getTopUnit());

                        // Second step: apply the denominator multiplicator from base to the value
                        // e.g. : 50 000 g/km ==>  50 000 * 100 000 g / cm
                        $unitValue->value /= $this->denominator->getMultiplicatorFromBase();

                        // Third step; convert the other measure
                        $measureValue = (new self())
                            ->setValue($measure->value)
                            ->setUnit($measure->unit);
                        $measureValue->convert($measure->getUnit()->getTopUnit());
                        $measureValue->value /= $measure->getDenominator()->getMultiplicatorFromBase();

                        $this->value = $unitValue->value + $measureValue->value;
                        $this->unit = $this->unit->getTopUnit();
                        $this->denominator = $this->denominator->getTopUnit();
                        $this->code = $this->unit->getTopUnit()->getCode().'/'.$this->denominator->getTopUnit()->getCode();

                        // Finally convert it back to the original composed unit
                        // e.g. : 50 000 * 100 000 g / cm  ==> 50 000 g/km
                        $this->value *= $originalDenominator->getMultiplicatorFromBase();
                        $this->denominator = $originalDenominator;

                        // e.g. : 50 000 g / km ==> 50 kg/km
                        $this->value *= $originalUnit->getMultiplicatorFromBase();
                        $this->unit = $originalUnit;
                        $this->code = $originalUnit->getCode().'/'.$originalDenominator->getCode();
                    } else {
                        throw new Exception('The two measures cannot be substracted as they are not of same type');
                    }
                } else {
                    throw new Exception('The two measures cannot be substracted as they are not of same type');
                }
            } else {
                // check if we're talking about the same type of unit (e.g.: we can't add a kg to meters)
                if ($this->unit->getTopUnit()->getCode() === $measure->unit->getTopUnit()->getCode()) {
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
                    throw new Exception('The two measures cannot be added as they are not of same type');
                }
            }
        }

        return $this;
    }

    // Converts a measure to the wanted unit
    final public function convert(Unit $unit): self {

        // The unit to convert to isn't in the same family
        if ($this->unit->getTopUnit()->getCode() !== $unit->getTopUnit()->getCode()) {
            throw new Exception('The measure cannot be converted to another type of measure that is not in the same Unit tree');
        }

        // The unit to transform to is not the same
        if ($this->unit !== $unit) {
            $this->value *= $this->unit->getMultiplicatorFromBase() / $unit->getMultiplicatorFromBase();
            $this->unit = $unit;
            $this->code = $unit->getCode();
        }

        return $this;
    }

    // Check if the measures have the same code
    final public function equals(self $measure): bool {
        return $this->code == $measure->getCode();
    }

    final public function getCode(): ?string {
        return $this->code;
    }

    final public function getDenominator(): ?Unit {
        return $this->denominator;
    }

    final public function getUnit(): Unit {
        return $this->unit;
    }

    final public function getValue(): ?float {
        return $this->value;
    }

    final public function setCode(?string $code): self {
        $this->code = $code;

        return $this;
    }

    final public function setDenominator(Unit $denominator): self {
        $this->denominator = $denominator;
        $this->code = $this->code.'/'.$denominator->getCode();

        return $this;
    }

    final public function setUnit(Unit $unit): self {
        $this->unit = $unit;
        $this->code = $unit->getCode();

        return $this;
    }

    final public function setValue(?float $value): self {
        $this->value = $value;

        return $this;
    }

    final public function substract(self $measure): self {
        // The measure's code is the same, then use a simple substraction (g - g = g ; g/m + g/m = g/m)
        if ($this->equals($measure)) {
            $this->value -= $measure->getValue();
        } else { // If the two codes ain't the same

            // We have a composed unit
            if (null !== $this->denominator) {

                // check if there is a denominator to the second measure too
                if (null !== $measure->getDenominator()) {
                    // Check if both measure are from the same family tree
                    if (
                        $this->unit->getTopUnit()->getCode() === $measure->getUnit()->getTopUnit()->getCode()
                        && $this->denominator->getTopUnit()->getCode() === $measure->getDenominator()->getTopUnit()->getCode()
                    ) {
                        // Let's convert and do the maths
                        $originalUnit = $this->unit;
                        $originalDenominator = $this->denominator;

                        // First step: convert the unit to the smallest value (unit#base = 0)
                        // e.g. : 50 kg/km ==> 50 000 g / km
                        $unitValue = (new self())
                            ->setValue($this->value)
                            ->setUnit($this->unit);
                        $unitValue->convert($this->unit->getTopUnit());

                        // Second step: apply the denominator multiplicator from base to the value
                        // e.g. : 50 000 g/km ==>  50 000 * 100 000 g / cm
                        $unitValue->value /= $this->denominator->getMultiplicatorFromBase();

                        // Third step; convert the other measure
                        $measureValue = (new self())
                            ->setValue($measure->value)
                            ->setUnit($measure->unit);
                        $measureValue->convert($measure->getUnit()->getTopUnit());
                        $measureValue->value /= $measure->getDenominator()->getMultiplicatorFromBase();

                        $this->value = $unitValue->value - $measureValue->value;
                        $this->unit = $this->unit->getTopUnit();
                        $this->denominator = $this->denominator->getTopUnit();
                        $this->code = $this->unit->getTopUnit()->getCode().'/'.$this->denominator->getTopUnit()->getCode();

                        // Finally convert it back to the original composed unit
                        // e.g. : 50 000 * 100 000 g / cm  ==> 50 000 g/km
                        $this->value *= $originalDenominator->getMultiplicatorFromBase();
                        $this->denominator = $originalDenominator;

                        // e.g. : 50 000 g / km ==> 50 kg/km
                        $this->value *= $originalUnit->getMultiplicatorFromBase();
                        $this->unit = $originalUnit;
                        $this->code = $originalUnit->getCode().'/'.$originalDenominator->getCode();
                    } else {
                        throw new Exception('The two measures cannot be substracted as they are not of same type');
                    }
                } else {
                    throw new Exception('The two measures cannot be substracted as they are not of same type');
                }
            } else {
                // check if we're talking about the same type of unit (e.g.: we can't substract a kg to meters nor a kg to a kg/m)
                if ($this->unit->getTopUnit()->getCode() === $measure->getUnit()->getTopUnit()->getCode() && null === $measure->getDenominator()) {
                    $originalUnit = $this->unit;

                    // to avoid float rounding problems, use the smallest mesure (the Unit#base=0 one, so the topCode)
                    $firstValue = $this->convert($this->unit->getTopUnit());
                    $secondValue = $measure->convert($measure->getUnit()->getTopUnit());

                    // Apply the changes to the entity
                    $this->value = $firstValue->getValue() - $secondValue->getValue();
                    $this->unit = $this->unit->getTopUnit();
                    $this->code == $this->unit->getCode();

                    // Now convert it back to the wanted Unit
                    $this->convert($originalUnit);
                } else { // this is not the same unit
                    throw new Exception('The two measures cannot be substracted as they are not of same type');
                }
            }
        }

        return $this;
    }
}
