<?php

namespace App\Repository\Purchase\Supplier;

use App\Doctrine\DBAL\Types\Embeddable\BlockerStateType;
use App\Doctrine\DBAL\Types\Embeddable\CloserStateType;
use App\Doctrine\DBAL\Types\Embeddable\Purchase\Order\CloserStateType as ItemCloserStateType;
use App\Doctrine\DBAL\Types\Embeddable\Purchase\Order\ItemStateType;
use App\Doctrine\DBAL\Types\Embeddable\Purchase\Order\OrderStateType;
use App\Entity\Purchase\Supplier\Supplier;
use App\Security\SecurityTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @extends ServiceEntityRepository<Supplier>
 *
 * @method null|Supplier find($id, $lockMode = null, $lockVersion = null)
 * @method null|Supplier findOneBy(array $criteria, ?array $orderBy = null)
 * @method Supplier[]    findAll()
 * @method Supplier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class SupplierRepository extends ServiceEntityRepository {
    use SecurityTrait {
        __construct as private securityConstruct;
    }

    public function __construct(ManagerRegistry $registry, Security $security) {
        parent::__construct($registry, Supplier::class);
        $this->securityConstruct($security);
    }

    /**
     * @return Supplier[]
     */
    public function findByReceipt(): array {
        /** @phpstan-ignore-next-line */
        return $this->createQueryBuilder('s')
            ->addSelect('o')
            ->innerJoin(
                join: 's.orders',
                alias: 'o',
                conditionType: Join::WITH,
                condition: sprintf(
                    'o.deliveryCompany = :company AND o.embBlocker.state != \'%s\' AND o.embState.state IN (\'%s\', \'%s\')',
                    CloserStateType::TYPE_STATE_CLOSED,
                    OrderStateType::TYPE_STATE_AGREED,
                    OrderStateType::TYPE_STATE_PARTIALLY_DELIVERED
                )
            )
            ->setParameter('company', $this->getCompanyId())
            ->innerJoin(
                join: 'o.items',
                alias: 'i',
                conditionType: Join::WITH,
                condition: sprintf(
                    'i.embBlocker.state != \'%s\' AND i.embState.state IN (\'%s\', \'%s\')',
                    ItemCloserStateType::TYPE_STATE_CLOSED,
                    ItemStateType::TYPE_STATE_AGREED,
                    ItemStateType::TYPE_STATE_PARTIALLY_DELIVERED
                )
            )
            ->where(sprintf('s.embBlocker.state != \'%s\'', BlockerStateType::TYPE_STATE_DISABLED))
            ->getQuery()
            ->getResult();
    }
}
