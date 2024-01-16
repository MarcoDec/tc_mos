<?php

namespace App\Repository\Project\Product;

use App\Doctrine\DBAL\Types\Embeddable\BlockerStateType;
use App\Entity\Project\Product\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method null|Product find($id, $lockMode = null, $lockVersion = null)
 * @method null|Product findOneBy(array $criteria, ?array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ProductRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Product::class);
    }

    public function expires(): void {
        $this->_em->createQueryBuilder()
            ->update($this->getClassName(), 'p')
            ->set('p.embBlocker.state', ':place')
            ->setParameter('place', BlockerStateType::TYPE_STATE_DISABLED)
            ->where('p.endOfLife >= NOW()')
            ->getQuery()
            ->execute();
    }
    public function findByEmbBlockerAndEmbState(): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.embBlocker.state = :enabled')
            ->andWhere('i.embState.state IN (:states)')
            ->setParameters([
                'enabled' => 'enabled',
                'states' => ['agreed', 'to_validate'],
            ])
            ->getQuery()
            ->getResult();
    }
}
