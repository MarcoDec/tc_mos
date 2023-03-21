<?php

namespace App\DataProvider\Project\Operation;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Project\Operation\Type;
use App\Paginator;
use App\Repository\Project\Operation\TypeRepository;

/**
 * @phpstan-import-type Order from TypeRepository
 *
 * @phpstan-type Context array{filters?: array{assembly?: 'false'|'true', name?: string, page?: string, order?: Order}}
 */
final class TypeDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface {
    public function __construct(private readonly int $itemPerPage, private readonly TypeRepository $repo) {
    }

    /**
     * @param Context $context
     *
     * @return Paginator<Type>
     */
    public function getCollection(string $resourceClass, ?string $operationName = null, array $context = []): Paginator {
        $filters = [];
        if (isset($context['filters'])) {
            if (isset($context['filters']['assembly'])) {
                $filters['assembly'] = $context['filters']['assembly'] === 'true';
            }
            if (isset($context['filters']['name'])) {
                $filters['name'] = $context['filters']['name'];
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

    /** @param Context $context */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        return $resourceClass === Type::class && $operationName === 'get';
    }
}
