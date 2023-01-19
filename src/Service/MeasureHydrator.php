<?php

namespace App\Service;

use App\Entity\Embeddable\Measure;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Unit;
use App\Repository\Management\UnitRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Cache\CacheInterface;

final class MeasureHydrator {
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly UnitRepository $repo,
        private readonly RequestStack $stack
    ) {
    }

    public function hydrate(Measure $measure): Measure {
        if (!$this->isSafe()) {
            return $measure;
        }
        $measure->setUnit($this->getUnit($measure->getCode()));
        $measure->setDenominatorUnit($this->getUnit($measure->getDenominator()));
        return $measure;
    }

    public function hydrateIn(MeasuredInterface $entity): MeasuredInterface {
        if ($this->isSafe()) {
            foreach ($entity->getMeasures() as $measure) {
                $this->hydrate($measure);
            }
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

    private function isSafe(): bool {
        $request = $this->stack->getCurrentRequest();
        if (empty($request)) {
            return true;
        }
        if (!$request->isMethod(Request::METHOD_GET)) {
            return true;
        }
        return !($request->attributes->get('_api_collection_operation_name') === 'options');
    }
}
