<?php

namespace App\DataProvider\Security;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Security\User;
use App\Repository\Security\UserRepository;

final class UserProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface {
    public function __construct(private UserRepository $repo) {
    }

    /**
     * @param string  $id
     * @param mixed[] $context
     */
    public function getItem(string $resourceClass, $id, ?string $operationName = null, array $context = []): ?User {
        return $this->repo->find($id);
    }

    /**
     * @param mixed[] $context
     */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        return $resourceClass === User::class
            && $operationName === 'get'
            && isset($context['operation_type'])
            && $context['operation_type'] === 'item';
    }
}
