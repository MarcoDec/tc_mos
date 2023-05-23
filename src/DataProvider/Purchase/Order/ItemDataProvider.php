<?php

namespace App\DataProvider\Purchase\Order;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Purchase\Order\ComponentItem;
use App\Entity\Purchase\Order\Item;
use App\Entity\Purchase\Order\Order;
use App\Entity\Purchase\Order\ProductItem;
use App\Repository\Purchase\Order\ItemRepository;

/**
 * @phpstan-type ItemContext array{filters?: array{'embState.state'?: string[], order?: string, page?: string, pagination?: string}}
 */
final class ItemDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface {
    /**
     * @param ItemRepository<ComponentItem|ProductItem> $repo
     */
    public function __construct(
        private readonly IriConverterInterface $iriConverter,
        private readonly int $itemPerPage,
        private readonly ItemRepository $repo
    ) {
    }

    /**
     * @param ItemContext $context
     *
     * @return (ComponentItem|ProductItem)[]
     */
    public function getCollection(string $resourceClass, ?string $operationName = null, array $context = []): array {
        $filters = [];
        if (isset($context['filters'])) {
            if (isset($context['filters']['embState.state'])) {
                $filters['embState.state'] = $context['filters']['embState.state'];
            }
            if (isset($context['filters']['order'])) {
                /** @var Order $order */
                $order = $this->iriConverter->getItemFromIri($context['filters']['order']);
                $filters['order'] = $order;
            }
        }
        return isset($context['filters']['page'])
            ? $this->repo->findBy(
                criteria: $filters,
                limit: $this->itemPerPage,
                offset: ((int) ($context['filters']['page']) - 1) * $this->itemPerPage
            )
            : $this->repo->findBy($filters);
    }

    /**
     * @param ItemContext $context
     */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        return $resourceClass === Item::class && $operationName === 'get';
    }
}
