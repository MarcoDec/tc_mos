<?php

namespace App\DataProvider\Logistics;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Logistics\Warehouse;
use App\Repository\Logistics\WarehouseRepository;

final class WarehouseDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface {
    public function __construct(private readonly WarehouseRepository $repo) {
    }

    /**
     * @return Warehouse[]
     */
    public function getCollection(string $resourceClass, ?string $operationName = null): array {
        return $this->repo->findImport();
    }

    /**
     * @param mixed[] $context
     */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        return $resourceClass === Warehouse::class && $operationName === 'import';
    }
}
