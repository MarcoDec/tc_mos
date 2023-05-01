<?php

namespace App\Repository\Management\Company;

use App\Doctrine\DBAL\Types\Embeddable\BlockerStateType;
use App\Doctrine\DBAL\Types\Embeddable\CloserStateType;
use App\Doctrine\DBAL\Types\Embeddable\Purchase\Order\CloserStateType as ItemCloserStateType;
use App\Doctrine\DBAL\Types\Embeddable\Purchase\Order\ItemStateType;
use App\Doctrine\DBAL\Types\Embeddable\Purchase\Order\OrderStateType;
use App\Entity\Management\Society\Company\Company;
use App\Security\SecurityTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @extends ServiceEntityRepository<Company>
 *
 * @method null|Company find($id, $lockMode = null, $lockVersion = null)
 * @method null|Company findOneBy(array $criteria, ?array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class CompanyRepository extends ServiceEntityRepository {
    use SecurityTrait {
        __construct as private securityConstruct;
    }

    public function __construct(ManagerRegistry $registry, Security $security) {
        parent::__construct($registry, Company::class);
        $this->securityConstruct($security);
    }

}
