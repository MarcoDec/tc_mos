<?php

namespace App\DataProvider\Selling\Customer;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Selling\Customer\Product;
use App\Repository\Selling\Customer\ProductRepository;

final class ProductDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface {
    public function __construct(private readonly ProductRepository $repo) {
    }

    /**
     * @param int     $id
     * @param mixed[] $context
     */
    public function getItem(string $resourceClass, $id, ?string $operationName = null, array $context = []): ?Product {
        return $this->repo->findWithUnit($id);
    }

    /**
     * @param mixed[] $context
     */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        return $resourceClass === Product::class
            && $operationName === 'get'
            && isset($context['openapi_definition_name'])
            && $context['openapi_definition_name'] === 'CustomerProductPrice-write';
    }
}
