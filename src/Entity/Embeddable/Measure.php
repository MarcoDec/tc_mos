<?php

namespace App\Entity\Embeddable;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Management\Unit;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class Measure {
    #[
        ApiProperty(description: 'Code ', example: 'g'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private ?string $code = null;

    #[
        ApiProperty(description: 'Dénominateur ', example: 'm'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private ?string $denominator = null;

    private ?Unit $denominatorUnit = null;
    private ?Unit $unit = null;

    #[
        ApiProperty(description: 'Valeur', example: '2.66'),
        ORM\Column,
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private float $value = 0;

    final public function add(self $measure): self {
        if ($this->unit === null || $measure->unit === null) {
            throw new LogicException('Unit not loaded.');
        }
        if (!$this->unit->has($measure->unit)) {
            throw new LogicException("Units {$this->unit->getCode()} and {$measure->unit->getCode()} aren't on the same family.");
        }
        if (
            ($this->denominator !== null && $this->denominatorUnit === null)
            || ($measure->denominator !== null && $measure->denominatorUnit === null)
        ) {
            throw new LogicException('Unit not loaded.');
        }
        if ($this->denominatorUnit !== null && !$this->denominatorUnit->has($measure->denominatorUnit)) {
            throw new LogicException("Units {$this->denominatorUnit->getCode()} and {$measure->denominatorUnit?->getCode()} aren't on the same family.");
        }
        if ($this->unit->isLessThan($measure->unit)) {
            if ($this->denominatorUnit !== null && $measure->denominatorUnit !== null) {
                if ($this->denominatorUnit->isLessThan($measure->denominatorUnit)) {
                    $inverse = (new self())
                        ->setCode($measure->denominator)
                        ->setUnit($measure->denominatorUnit)
                        ->setValue(1 / $measure->value)
                        ->convert($this->denominatorUnit);
                    $measure = (new self())
                        ->setCode($measure->code)
                        ->setDenominator($measure->denominator)
                        ->setDenominatorUnit($measure->denominatorUnit)
                        ->setUnit($measure->unit)
                        ->setValue(1 / $inverse->value);
                } else {
                    $inverse = (new self())
                        ->setCode($this->denominator)
                        ->setUnit($this->denominatorUnit)
                        ->setValue(1 / $this->value)
                        ->convert($measure->denominatorUnit);
                    $this
                        ->setDenominator($inverse->code)
                        ->setDenominatorUnit($inverse->unit)
                        ->setValue(1 / $inverse->value);
                }
            }
            $this->value = $this->value + $measure->convert($this->unit)->value;
        } else {
            $measure->add($this);
        }
        return $this;
    }

    final public function convert(Unit $unit): self {
        if ($this->unit === null) {
            throw new LogicException('Unit not loaded.');
        }
        if (!$this->unit->has($unit)) {
            throw new LogicException("Units {$this->unit->getCode()} and {$unit->getCode()} aren't on the same family.");
        }
        if ($this->denominator !== null && $this->denominatorUnit === null) {
            throw new LogicException('Unit not loaded.');
        }
        $this->code = $unit->getCode();
        $this->unit = $unit;
        $this->value *= $this->unit->getDistance($unit);
        return $this;
    }

    final public function getCode(): ?string {
        return $this->code;
    }

    final public function getDenominator(): ?string {
        return $this->denominator;
    }

    final public function getDenominatorUnit(): ?Unit {
        return $this->denominatorUnit;
    }

    final public function getUnit(): ?Unit {
        return $this->unit;
    }

    final public function getValue(): float {
        return $this->value;
    }

    final public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    final public function setDenominator(?string $denominator): self {
        $this->denominator = $denominator;
        return $this;
    }

    final public function setDenominatorUnit(?Unit $denominatorUnit): self {
        $this->denominatorUnit = $denominatorUnit;
        return $this;
    }

    final public function setUnit(?Unit $unit): self {
        $this->unit = $unit;
        return $this;
    }

    final public function setValue(float $value): self {
        $this->value = $value;
        return $this;
    }

    final public function substract(self $measure): self {
        return $this->add($measure->setValue(-$measure->value));
    }
}
