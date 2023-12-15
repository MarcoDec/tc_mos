<?php

namespace App\DataProvider\Purchase\Order;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Purchase\Order\ComponentItem;
use App\Entity\Purchase\Order\Item;
use App\Entity\Purchase\Order\ProductItem;
use App\Repository\Purchase\Order\ItemRepository;

final class ReceiptDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface {
    /**
     * @param ItemRepository<ComponentItem|ProductItem> $repo
     */
    public function __construct(private readonly ItemRepository $repo) {
    }

    /**
     * @param int     $id
     * @param mixed[] $context
     */
    public function getItem(string $resourceClass, $id, ?string $operationName = null, array $context = []): ComponentItem|ProductItem|null {
        return $this->repo->findOneByReceipt($id,$resourceClass);
    }

    /**
     * @param mixed[] $context
     */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        return ($resourceClass === Item::class || is_subclass_of($resourceClass, Item::class))
            && $operationName === 'get'
            && isset($context['openapi_definition_name'])
            && in_array($context['openapi_definition_name'], ['ComponentStock-receipt', 'ProductStock-receipt']);
    }
}
