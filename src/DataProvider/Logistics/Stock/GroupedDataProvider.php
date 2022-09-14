<?php

namespace App\DataProvider\Logistics\Stock;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Logistics\Stock\Stock;

/**
 * @phpstan-type ItemStock \App\Entity\Logistics\Stock\ComponentStock|\App\Entity\Logistics\Stock\ProductStock
 */
final class GroupedDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface {
    public function __construct(private readonly StockDataProvider $provider) {
    }

    /**
     * @param array{filters?: array{warehouse: string}} $context
     *
     * @return ItemStock[]
     */
    public function getCollection(string $resourceClass, ?string $operationName = null, array $context = []): array {
        $stocks = $this->provider->getCollection($resourceClass, $operationName, $context);
        /** @var array<string, ItemStock> $grouped */
        $grouped = [];
        if (empty($stocks)) {
            return $grouped;
        }
        $grouped = collect($grouped);
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
