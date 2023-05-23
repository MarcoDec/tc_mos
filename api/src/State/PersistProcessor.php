<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Generator;
use App\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;

class PersistProcessor implements ProcessorInterface {
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    /**
     * @param Entity|Generator $data
     * @param mixed[]          $uriVariables
     * @param mixed[]          $context
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Entity {
        if ($data instanceof Generator) {
            $data = $data->generate();
        }
        $this->em->beginTransaction();
        $this->em->persist($data);
        $this->em->flush();
        $this->postPersist($data);
        $this->em->commit();
        return $data;
    }

    protected function postPersist(Entity $data): void {
    }
}
