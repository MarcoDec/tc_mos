<?php

namespace App\Entity\Embeddable;

use App\Entity\Management\AbstractUnit;
use App\Entity\Management\Unit;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class Measure {
    public const GROUPS=['read:measure', 'write:measure',
        'read:balance-sheet', 'write:balance-sheet',
        'write:customer',
        'write:customer:accounting',
        'write:customer:logistics',
        'write:product',
        'write:product:logistics',
        'write:product:production',
        'write:product:project',
        'read:product-customer',
        'read:operation-employee:collection',
        'read:reference', 'write:reference',
        'read:production-quality-value', 'write:production-quality-value',
        'read:manufacturing-order',
    ];
    #[
        ORM\Column(length: AbstractUnit::UNIT_CODE_MAX_LENGTH, nullable: true, options: ['collation' => 'utf8mb3_bin']),
        Serializer\Groups(self::GROUPS)
    ]
    private ?string $code = null;

    #[
        ORM\Column(length: AbstractUnit::UNIT_CODE_MAX_LENGTH, nullable: true, options: ['collation' => 'utf8mb3_bin']),
        Serializer\Groups(self::GROUPS)
    ]
    private ?string $denominator = null;

    private ?AbstractUnit $denominatorUnit = null;
    private ?AbstractUnit $unit = null;

    #[
        ORM\Column(options: ['default' => 0]),
        Serializer\Groups(self::GROUPS)
    ]
    private float $value = 0;

    public function __construct(float $value = 0, ?string $code = null, ?string $denominator = null) {
        $this->value = $value;
        $this->code = $code;
        $this->denominator = $denominator;
        $this->unit = null;
        $this->denominatorUnit = null;
    }

    final public function add(self $measure): self {
        $measure = $this->convertToSame($measure);
        $this->value = $this->value + $measure->value;
        return $this;
    }

    final public function convert(AbstractUnit $unit, ?AbstractUnit $denominator = null): self {
        $safeUnit = $this->getSafeUnit();
//        dump(['convert','unit'=>$unit, 'this'=>$this, 'denominator'=>$denominator]);
        $safeUnit->assertSameAs($unit);
        if ($safeUnit->getCode() !== $unit->getCode()) {
            $this->value *= $safeUnit->getConvertorDistance($unit);
            $this->code = $unit->getCode();
            $this->unit = $unit;
        }

        if ($denominator !== null) {
            if ($this->denominator === null) {
                throw new LogicException('No denominator.');
            }
            if ($this->denominatorUnit === null) {
                throw new LogicException('Unit not loaded.');
            }
            $this->denominatorUnit->assertSameAs($denominator);
            if ($this->denominatorUnit->getCode() !== $denominator->getCode()) {
                $this->value *= 1 / $this->denominatorUnit->getConvertorDistance($denominator);
                $this->denominator = $denominator->getCode();
                $this->denominatorUnit = $denominator;
            }
        }
        return $this;
    }

    final public function getCode(): ?string {
        return $this->code;
    }

    final public function getDenominator(): ?string {
        return $this->denominator;
    }

    final public function getDenominatorUnit(): ?AbstractUnit {
        return $this->denominatorUnit;
    }

    final public function getSafeUnit(): AbstractUnit {
        if ($this->unit === null) {
            throw new LogicException('Unit not loaded.');
        }
        return $this->unit;
    }

    final public function getUnit(): ?AbstractUnit {
        return $this->unit;
    }

    final public function getValue(): float {
        return $this->value;
    }

    final public function isGreaterThanOrEqual(self $measure): bool {
        $clone = clone $this;
        $measure = $clone->convertToSame($measure);
        return $clone->value >= $measure->value;
    }

    final public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    final public function setDenominator(?string $denominator): self {
        $this->denominator = $denominator;
        return $this;
    }

    final public function setDenominatorUnit(?AbstractUnit $denominatorUnit): self {
        $this->denominatorUnit = $denominatorUnit;
        return $this;
    }

    final public function setUnit(?AbstractUnit $unit): self {
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

    private function convertToSame(self $measure): self {
        /** @var AbstractUnit $unit */
        $unit = $this->getSafeUnit()->getLess($measure->getSafeUnit());
//        dump(['convertToSame','unit'=>$unit, 'this'=>$this, 'measure'=>$measure]);
        /** @var null|AbstractUnit $denominator */
        $denominator = $this->denominatorUnit !== null && $measure->denominatorUnit !== null
            ? $this->denominatorUnit->getLess($measure->denominatorUnit)
            : null;
        $this->convert($unit, $denominator);
        return (clone $measure)->convert($unit, $denominator);
    }
}
