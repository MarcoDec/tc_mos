<?php

namespace App\DataProvider\Purchase\Component;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Management\Unit;
use App\Entity\Purchase\Component\Attribute;
use App\Paginator;
use App\Repository\Purchase\Component\AttributeRepository;

/**
 * @phpstan-import-type Order from AttributeRepository
 *
 * @phpstan-type Context array{filters?: array{description?: string, page?: string, name?: string, order?: Order, type?: string, unit?: string}}
 */
final class AttributeDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface {
    public function __construct(
        private readonly IriConverterInterface $iriConverter,
        private readonly int $itemPerPage,
        private readonly AttributeRepository $repo
    ) {
    }

    /**
     * @param Context $context
     *
     * @return Paginator<Attribute>
     */
    public function getCollection(string $resourceClass, ?string $operationName = null, array $context = []): Paginator {
        $filters = [];
        if (isset($context['filters'])) {
            if (isset($context['filters']['description'])) {
                $filters['description'] = $context['filters']['description'];
            }
            if (isset($context['filters']['name'])) {
                $filters['name'] = $context['filters']['name'];
            }
            if (isset($context['filters']['unit'])) {
                /** @var Unit $unit */
                $unit = $this->iriConverter->getItemFromIri($context['filters']['unit']);
                $filters['unit'] = $unit;
            }
            if (isset($context['filters']['type'])) {
                $filters['type'] = $context['filters']['type'];
            }
        }
        $page = (int) ($context['filters']['page'] ?? 1);
        return $this->repo->findByPaginated(
            page: $page,
            criteria: $filters,
            limit: $this->itemPerPage,
            offset: ($page - 1) * $this->itemPerPage,
            orderBy: $context['filters']['order'] ?? null
        );
    }

    /**
     * @param Context $context
     */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        return $resourceClass === Attribute::class && $operationName === 'gete';
    }
}
