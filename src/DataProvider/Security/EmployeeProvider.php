<?php

namespace App\DataProvider\Security;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Security\User;
use App\Repository\Security\UserRepository;

final class EmployeeProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface {
    public function __construct(private UserRepository $repo) {
    }

    /**
     * @param string  $id
     * @param mixed[] $context
     */
    public function getItem(string $resourceClass, $id, ?string $operationName = null, array $context = []): ?User {
       if (intval($id)>0) {
          $search= $this->repo->findBy(['id'=>$id]);
          if (count($search)>0) return $search[0];
       }
       return null;
    }

    /**
     * @param mixed[] $context
     */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
       return $resourceClass === User::class
          && $operationName === 'get'
          && isset($context['operation_type'])
          && $context['operation_type'] === 'collection';
    }
}
