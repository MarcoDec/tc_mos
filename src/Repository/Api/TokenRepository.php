<?php

namespace App\Repository\Api;

use App\Entity\Api\Token;
use App\Entity\Hr\Employee\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Token>
 *
 * @method null|Token find($id, $lockMode = null, $lockVersion = null)
 * @method Token[]    findAll()
 * @method Token[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class TokenRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Token::class);
    }

    public function connect(Employee $user): void {
        $this->disconnect($user);
        $this->_em->persist($token = new Token($user));
        $user->addApiToken($token);
        $this->_em->flush();
    }

    public function disconnect(Employee $user): void {
        $this->_em->createQueryBuilder()
            ->update($this->getClassName(), 't')
            ->set('t.expireAt', 'DATE_SUB(t.expireAt, 1, \'MINUTE\')')
            ->where('t.employee = :employee')
            ->setParameter('employee', $user->getId())
            ->getQuery()
            ->execute();
    }

    /**
     * @param array{token: string} $criteria
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): ?Token {
        $query = $this->createQueryBuilder('t')
            ->addSelect('a')
            ->addSelect('c')
            ->addSelect('e')
            ->innerJoin('t.employee', 'e', Join::WITH, 'e.deleted = FALSE')
            ->leftJoin('e.apiTokens', 'a')
            ->leftJoin('e.company', 'c', Join::WITH, 'c.deleted = FALSE')
            ->where('t.token = :token')
            ->setParameter('token', $criteria['token'])
            ->getQuery();
        try {
            /** @phpstan-ignore-next-line */
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            return null;
        }
    }

    public function renew(Employee $user): void {
        $this->_em->getConnection()->executeQuery(
            sql: "UPDATE {$this->getClassMetadata()->getTableName()} SET expire_at = DATE_ADD(expire_at, INTERVAL 1 HOUR) WHERE employee_id = :employee ORDER BY expire_at DESC LIMIT 1",
            params: ['employee' => $user->getId()]
        );
    }
}
