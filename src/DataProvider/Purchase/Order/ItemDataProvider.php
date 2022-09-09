<?php

namespace App\DataProvider\Purchase\Order;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Selling\Order\ComponentItem;
use App\Entity\Selling\Order\Item;
use App\Entity\Selling\Order\ProductItem;
use App\Repository\Selling\Order\ItemRepository;

final class ItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface {
    /**
     * @param ItemRepository<ComponentItem|ProductItem> $repo
     */
    public function __construct(private readonly ItemRepository $repo) {
    }

    /**
     * @param int     $id
     * @param mixed[] $context
     *
     * @return ComponentItem|null|ProductItem
     */
    public function getItem(string $resourceClass, $id, ?string $operationName = null, array $context = []): ?Item {
        return $this->repo->findOneByPatch($id);
    }

    /**
     * @param mixed[] $context
     */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        return $resourceClass === Item::class && $operationName === 'patch';
    }
}
