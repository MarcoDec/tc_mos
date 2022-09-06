<?php

namespace App\DataProvider\Purchase\Supplier;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Purchase\Supplier\Supplier;
use App\Repository\Purchase\Supplier\SupplierRepository;

final class SupplierDateProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface {
    public function __construct(private readonly SupplierRepository $repo) {
    }

    /**
     * @return Supplier[]
     */
    public function getCollection(string $resourceClass, ?string $operationName = null): array {
        return $this->repo->findByReceipt();
    }

    /**
     * @param mixed[] $context
     */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        return $resourceClass === Supplier::class && $operationName === 'get-receipts';
    }
}
