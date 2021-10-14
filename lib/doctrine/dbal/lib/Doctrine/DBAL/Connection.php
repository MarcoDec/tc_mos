<?php

namespace App\Doctrine\DBAL;

use App\Doctrine\ORM\Decorator\EntityManagerDecorator;
use App\Entity\Entity;
use Doctrine\DBAL\Connection as DoctrineConnection;

final class Connection extends DoctrineConnection {
    private EntityManagerDecorator $em;

    public function delete($table, array $criteria, array $types = []): int {
        return ($class = $this->em->getClassNameFor($table)) && is_subclass_of($class, Entity::class)
            ? $this->update($table, ['deleted' => true], $criteria, $types)
            : parent::delete($table, $criteria, $types);
    }

    public function setEm(EntityManagerDecorator $em): self {
        $this->em = $em;
        return $this;
    }
}
