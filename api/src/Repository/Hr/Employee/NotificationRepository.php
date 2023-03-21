<?php

declare(strict_types=1);

namespace App\Repository\Hr\Employee;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGenerator;
use App\Doctrine\ORM\QueryBuilder;
use App\Doctrine\Type\Hr\Employee\EnumNotificationType;
use App\Entity\Hr\Employee\Notification;
use App\Repository\Provider\ProviderInterface;
use App\Repository\Provider\ProviderTrait;
use App\Security\SecurityTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @extends ServiceEntityRepository<Notification>
 * @implements ProviderInterface<Notification>
 */
class NotificationRepository extends ServiceEntityRepository implements ProviderInterface {
    use ProviderTrait;
    use SecurityTrait {
        __construct as private constructSecurity;
    }

    /**
     * @param QueryCollectionExtensionInterface[] $collectionExtensions
     * @param QueryItemExtensionInterface[]       $itemExtensions
     */
    public function __construct(
        /** @phpstan-ignore-next-line */
        private readonly ManagerRegistry $managerRegistry,
        private readonly iterable $collectionExtensions,
        private readonly iterable $itemExtensions,
        Security $security
    ) {
        parent::__construct($managerRegistry, Notification::class);
        $this->constructSecurity($security);
    }

    public function readBy(EnumNotificationType $category): void {
        $this->_em->beginTransaction();
        $this->createReadQueryBuilder($category)->getQuery()->execute();
        $this->_em->commit();
    }

    public function remove(Notification $notification): Notification {
        $this->_em->beginTransaction();
        $notification->setRead(true);
        $this->createRemoveQueryBuilder()
            ->where('n.id = :id')
            ->setParameter('id', $notification->getId())
            ->getQuery()
            ->execute();
        $this->_em->commit();
        return $notification;
    }

    public function removeBy(EnumNotificationType $category): void {
        $this->_em->beginTransaction();
        $this->createRemoveQueryBuilder($category)->getQuery()->execute();
        $this->_em->commit();
    }

    private function createReadQueryBuilder(?EnumNotificationType $category = null): QueryBuilder {
        $qb = (new QueryBuilder($this->_em))->update($this->getClassName(), 'n')->set('n.read', true);
        if ($category !== null) {
            $qb
                ->where('n.category = :category')
                ->andWhere('n.user = :user')
                ->setParameters(['category' => $category->value, 'user' => $this->getUserId()]);
        }
        return $qb;
    }

    private function createRemoveQueryBuilder(?EnumNotificationType $category = null): QueryBuilder {
        return $this->createReadQueryBuilder($category)->set('n.deleted', true);
    }

    private function joinCollection(QueryBuilder $qb, QueryNameGenerator $generator): QueryBuilder {
        $param = $generator->generateParameterName('user');
        return $this
            ->resetDQLJoin($qb)
            ->andWhere("{$qb->getRootAliases()[0]}.user = :$param")
            ->setParameter($param, $this->getUserId());
    }
}
