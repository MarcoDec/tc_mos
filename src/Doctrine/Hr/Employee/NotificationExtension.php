<?php

namespace App\Doctrine\Hr\Employee;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Hr\Employee\Notification;
use App\Security\SecurityTrait;
use Doctrine\ORM\QueryBuilder;

final class NotificationExtension implements QueryCollectionExtensionInterface {
    use SecurityTrait;

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?string $operationName = null): void {
        if ($resourceClass === Notification::class) {
            $field = 'user';
            $param = $queryNameGenerator->generateParameterName($field);
            $queryBuilder
                ->andWhere("{$queryBuilder->getRootAliases()[0]}.$field = :$param")
                ->setParameter($param, $this->getUserId());
        }
    }
}
