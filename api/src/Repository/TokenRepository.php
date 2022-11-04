<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Hr\Employee\Employee;
use App\Entity\Token;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Token> */
class TokenRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Token::class);
    }

    public function connect(Employee $user): void {
        $this->_em->persist(new Token($user));
        $this->_em->flush();
    }

    public function renew(Employee $user): void {
        $this->_em->getConnection()->executeQuery(
            sql: "UPDATE `{$this->getClassMetadata()->getTableName()}` SET `expire_at` = DATE_ADD(`expire_at`, INTERVAL 1 HOUR) WHERE `deleted` = FALSE AND `employee_id` = :id ORDER BY `expire_at` DESC LIMIT 1",
            params: ['id' => $user->getId()]
        );
    }
}
