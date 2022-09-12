<?php

namespace App\Service;

use App\Entity\Embeddable\Measure;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Unit;
use Doctrine\ORM\EntityManagerInterface;

final class MeasureHydrator {
    /** @var array<string, Unit> */
    private array $units = [];

    public function __construct(private readonly EntityManagerInterface $em) {
    }

    public function hydrate(Measure $measure): Measure {
        $measure->setUnit($this->getUnit($measure->getCode()));
        $measure->setDenominatorUnit($this->getUnit($measure->getDenominator()));
        return $measure;
    }

    public function hydrateIn(MeasuredInterface $entity): MeasuredInterface {
        foreach ($entity->getMeasures() as $measure) {
            $this->hydrate($measure);
        }
        return $entity;
    }

    private function getUnit(?string $code): ?Unit {
        if ($code !== null) {
            if (isset($this->units[$code])) {
                return $this->units[$code];
            }
            /** @var null|Unit $unit */
            $unit = $this->em->getRepository(Unit::class)->findOneBy(['code' => $code]);
            if ($unit !== null) {
                $this->units[$code] = $unit;
            }
            return $unit;
        }
        return null;
    }
}
