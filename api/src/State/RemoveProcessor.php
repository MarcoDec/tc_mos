<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;

class RemoveProcessor implements ProcessorInterface {
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    /**
     * @param Entity  $data
     * @param mixed[] $uriVariables
     * @param mixed[] $context
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Entity {
        $this->em->beginTransaction();
        $this->em
            ->createQueryBuilder()
            ->update($operation->getClass(), 'd')
            ->set('d.deleted', true)
            ->where('d.id = :id')
            ->setParameter('id', $data->getId())
            ->getQuery()
            ->execute();
        $this->em->commit();
        return $data;
    }
}
