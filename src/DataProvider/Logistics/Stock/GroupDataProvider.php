<?php

namespace App\DataProvider\Logistics\Stock;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Logistics\Stock\Group;
use App\Entity\Logistics\Warehouse;
use App\Paginator;
use App\Repository\Logistics\Stock\GroupRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/** @phpstan-type Context array{filters?: array{location?: null|string, page?: int, warehouse?: string}} */
class GroupDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface {
    public function __construct(
        private readonly IriConverterInterface $iriConverter,
        private readonly int $itemPerPage,
        private readonly GroupRepository $repo
    ) {
    }

    /**
     * @param Context $context
     *
     * @return Paginator<Group>
     */
    public function getCollection(string $resourceClass, ?string $operationName = null, array $context = []): Paginator {
        if (!isset($context['filters']['warehouse'])) {
            throw new BadRequestHttpException('Missing warehouse filter.');
        }
        /** @var Warehouse $warehouse */
        $warehouse = $this->iriConverter->getItemFromIri($context['filters']['warehouse']);
        $page = (int) ($context['filters']['page'] ?? 1);
        return $this->repo->findByPaginated(
            page: $page,
            limit: $this->itemPerPage,
            offset: ($page - 1) * $this->itemPerPage,
            warehouse: $warehouse,
            location: $context['filters']['location'] ?? null
        );
    }

    /** @param Context $context */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        return $resourceClass === Group::class;
    }
}
