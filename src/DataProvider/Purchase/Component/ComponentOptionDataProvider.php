<?php

namespace App\DataProvider\Purchase\Component;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Purchase\Component\Component;
use App\Repository\Purchase\Component\ComponentRepository;

final class ComponentOptionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface {
    public function __construct(private readonly ComponentRepository $repo) {
    }

    /** @return Component[] */
    public function getCollection(string $resourceClass, ?string $operationName = null): array {
        return $this->repo->findOptions();
    }

    /** @param mixed[] $context */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        return $resourceClass === Component::class && $operationName === 'options';
    }
}
