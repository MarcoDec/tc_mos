<?php

namespace App\Doctrine\ORM\Decorator;

use App\Doctrine\DBAL\Connection;
use Doctrine\ORM\Decorator\EntityManagerDecorator as DoctrineEntityManagerDecorator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * @method Connection getConnection()
 */
final class EntityManagerDecorator extends DoctrineEntityManagerDecorator {
    public function __construct(EntityManagerInterface $wrapped) {
        parent::__construct($wrapped);
        $this->getConnection()->setEm($this);
    }

    /**
     * @return class-string|null
     */
    public function getClassNameFor(string $table): ?string {
        /** @var ClassMetadata<object> $metadata */
        foreach ($this->getMetadataFactory()->getAllMetadata() as $metadata) {
            if ($metadata->getTableName() === $table) {
                return $metadata->getName();
            }
        }
        return null;
    }
}
