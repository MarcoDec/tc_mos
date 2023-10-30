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
     * @param string $resourceClass
     * @param int $id
     * @param string|null $operationName
     * @param array $context
     * @return Component|null
     */
    public function getItem(string $resourceClass, $id, ?string $operationName = null, array $context = []): ?Component {
        return $this->repo->find($id);
    }

    /**
     * @param string $resourceClass
     * @param string|null $operationName
     * @param array $context
     * @return bool
     */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        //dump (['$resourceClass'=>$resourceClass, 'operationName' => $operationName, '$context'=> $context, 'test' =>$resourceClass === Component::class && in_array($operationName, ['get', 'patch'])]);
        return $resourceClass === Component::class && in_array($operationName, ['get', 'patch']);
    }
}
