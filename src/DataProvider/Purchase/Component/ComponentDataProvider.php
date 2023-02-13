<?php

namespace App\DataProvider\Purchase\Component;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Purchase\Component\Component;
use App\Repository\Purchase\Component\ComponentRepository;

final class ComponentDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface {
    public function __construct(private readonly ComponentRepository $repo) {
    }

    /**
     * @param int     $id
     * @param mixed[] $context
     */
    public function getItem(string $resourceClass, $id, ?string $operationName = null, array $context = []): ?Component {
        return $this->repo->find($id);
    }

    /**
     * @param mixed[] $context
     */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        return $resourceClass === Component::class && $operationName === 'get';
    }
}
