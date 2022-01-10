<?php

namespace App\Repository\Api;

use App\Entity\Api\Token;
use App\Entity\Hr\Employee\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Token>
 *
 * @method null|Token find($id, $lockMode = null, $lockVersion = null)
 * @method null|Token findOneBy(array $criteria, array $orderBy = null)
 * @method Token[]    findAll()
 * @method Token[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class TokenRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Token::class);
    }

    public function connect(Employee $user): void {
        $this->disconnect($user);
        $this->_em->persist(new Token($user));
        $this->_em->flush();
    }

    public function disconnect(Employee $user): void {
        foreach ($this->findBy(['employees' => $user]) as $token) {
            if (!$token->isExpired()) {
                $token->expire();
            }
        }
    }
}
