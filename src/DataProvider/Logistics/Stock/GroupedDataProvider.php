<?php

namespace App\DataProvider\Logistics\Stock;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Logistics\Stock\Stock;
use App\Entity\Logistics\Warehouse;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @phpstan-type ItemStock \App\Entity\Logistics\Stock\ComponentStock|\App\Entity\Logistics\Stock\ProductStock
 */
final class GroupedDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface {
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly IriConverterInterface $iriConverter
    ) {
    }

    /**
     * @param array{filters?: array{warehouse: string}} $context
     *
     * @return ItemStock[]
     */
    public function getCollection(string $resourceClass, ?string $operationName = null, array $context = []): array {
        /** @var array<string, ItemStock> $grouped */
        $grouped = [];
        if (empty($context)) {
            return $grouped;
        }
        $grouped = collect($grouped);
        /** @var Warehouse $warehouse */
        $warehouse = $this->iriConverter->getItemFromIri($context['filters']['warehouse']);
        $stocks = $this->em->getRepository(Stock::class)->findByGrouped($warehouse);
        foreach ($stocks as $stock) {
            if (!empty($code = $stock->getGroupedId())) {
                if (isset($grouped[$code])) {
                    $grouped[$code]->group($stock);
                } else {
                    $grouped->put($code, clone $stock);
                }
            }
        }
        return $grouped->values()->all();
    }

    /**
     * @param mixed[] $context
     */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        return $resourceClass === Stock::class && $operationName === 'grouped';
    }
}
