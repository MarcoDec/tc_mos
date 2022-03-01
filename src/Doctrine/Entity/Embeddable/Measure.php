<?php

namespace App\Doctrine\Entity\Embeddable;

use App\Doctrine\Entity\Management\Unit;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class Measure {
    #[
        ORM\Column(nullable: true),
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private ?string $code = null;

    #[
        ORM\Column(nullable: true),
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private ?string $denominator = null;

    private ?Unit $denominatorUnit = null;
    private ?Unit $unit = null;

    #[
        ORM\Column(options: ['default' => 0]),
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
            $this->code = $measure->code;
            $this->denominator = $measure->denominator;
            $this->denominatorUnit = $measure->denominatorUnit;
            $this->unit = $measure->unit;
            $this->value = $measure->value;
        }
        return $this;
    }

    final public function convert(Unit $unit, ?Unit $denominator = null): self {
        if ($this->unit === null) {
            throw new LogicException('Unit not loaded.');
        }
        if (!$this->unit->has($unit)) {
            throw new LogicException("Units {$this->unit->getCode()} and {$unit->getCode()} aren't on the same family.");
        }
        $this->value *= $this->unit->getConvertorDistance($unit);
        $this->code = $unit->getCode();
        $this->unit = $unit;

        if ($denominator !== null) {
            if ($this->denominator === null) {
                throw new LogicException('No denominator.');
            }
            if ($this->denominatorUnit === null) {
                throw new LogicException('Unit not loaded.');
            }
            if (!$this->denominatorUnit->has($denominator)) {
                throw new LogicException("Units {$this->denominatorUnit->getCode()} and {$denominator->getCode()} aren't on the same family.");
            }
            $this->value *= 1 / $this->denominatorUnit->getConvertorDistance($denominator);
            $this->denominator = $denominator->getCode();
            $this->denominatorUnit = $denominator;
        }
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
