<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;

class PersistProcessor implements ProcessorInterface {
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    /**
     * @param Entity  $data
     * @param mixed[] $uriVariables
     * @param mixed[] $context
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Entity {
        $this->em->beginTransaction();
        $this->em->persist($data);
        $this->em->flush();
        $this->em->commit();
        return $data;
    }
}
