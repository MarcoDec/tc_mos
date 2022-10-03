<?php

namespace App\Service;

use App\Entity\Embeddable\Measure;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Unit;
use App\Repository\Management\UnitRepository;
use Symfony\Contracts\Cache\CacheInterface;

final class MeasureHydrator {
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly UnitRepository $repo
    ) {
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
        if (empty($code)) {
            return null;
        }
        $units = $this->cache->get('measure-units', fn () => $this->repo->loadAll());
        return $units[$code] ?? null;
    }
}
