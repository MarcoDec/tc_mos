<?php

namespace App\Service;

use App\Entity\Embeddable\Measure;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Currency;
use App\Entity\Management\Unit;
use App\Entity\Project\Product\Product;
use App\Repository\CurrencyRepository;
use App\Repository\Management\UnitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Proxy;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Cache\CacheInterface;
use Doctrine\Common\Util\ClassUtils;

final class MeasureHydrator {
    public function __construct(
        private readonly CacheInterface     $cache,
        private readonly UnitRepository     $unitRepo,
        private readonly CurrencyRepository $currencyRepo,
        private readonly RequestStack       $stack,
        private LoggerInterface             $logger,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function hydrateUnit(Measure $measure): Measure {
        if (!$this->isSafe()) {
            return $measure;
        }
        $measure->setUnit($this->getUnit($measure->getCode()));
        $measure->setDenominatorUnit($this->getUnit($measure->getDenominator()));
        return $measure;
    }
    public function hydrateCurrency(Measure $measure): Measure {
        if (!$this->isSafe()) {
            return $measure;
        }
        $measure->setUnit($this->getCurrency($measure->getCode()));
        $measure->setDenominatorUnit($this->getUnit($measure->getDenominator()));
        return $measure;
    }

    public function hydrateIn(MeasuredInterface $entity): MeasuredInterface {
        dump([
            "entity" => $entity,
            "entity class" => ClassUtils::getClass($entity)
        ]);

        if ($this->isSafe()) {
            $this->initializeEmbeddables($entity);
            foreach ($entity->getUnitMeasures() as $measure) {
                $this->hydrateUnit($measure);
            }
            foreach ($entity->getCurrencyMeasures() as $measure) {
                $this->hydrateCurrency($measure);
            }
        }
        return $entity;
    }

    private function initializeEmbeddables(MeasuredInterface $entity): void {
        if ($entity instanceof Product) {
            $product = $this->entityManager->find(Product::class, $entity->getId());
            dump(['product' => $product]);
            $embBlocker = $product->getEmbBlocker();
            dump(['embBlocker' => $product]);
        }
    }

    private function getUnit(?string $code): ?Unit {
        if (empty($code)) {
            return null;
        }
        $units = $this->cache->get('measure-units', fn () => $this->unitRepo->loadAll());
        return $units[$code] ?? null;
    }
    private function getCurrency(?string $code): ?Currency {
        if (empty($code)) {
            return null;
        }
        $currencies = $this->cache->get('measure-currencies', fn () => $this->currencyRepo->loadAll());
        return $currencies[$code] ?? null;
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
