<?php

namespace App\Doctrine\Hr\Employee;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Hr\Employee\Employee;
use App\Entity\Hr\Employee\Notification;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;
use UnexpectedValueException;

final class NotificationExtension implements QueryCollectionExtensionInterface {
    public function __construct(private Security $security) {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?string $operationName = null): void {
        if ($resourceClass === Notification::class) {
            $field = 'user';
            $param = $queryNameGenerator->generateParameterName($field);
            $queryBuilder
                ->andWhere("{$queryBuilder->getRootAliases()[0]}.$field = :$param")
                ->setParameter($param, $this->getUser());
        }
    }

    private function getUser(): int {
        $user = $this->security->getUser();
        if (!($user instanceof Employee)) {
            throw new UnexpectedValueException(sprintf('Expected argument of type "%s", "%s" given.', Employee::class, get_debug_type($user)));
        }
        $id = $user->getId();
        if (empty($id)) {
            throw new UnexpectedValueException(sprintf('Expected argument of type int, "%s" given.', get_debug_type($user)));
        }
        return $id;
    }
}
