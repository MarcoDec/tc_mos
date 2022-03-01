<?php

namespace App\Doctrine\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Doctrine\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;

class CloneDataPersister implements ContextAwareDataPersisterInterface {
    final public function __construct(protected EntityManagerInterface $em) {
    }

    /**
     * @param Entity  $data
     * @param mixed[] $context
     */
    final public function persist($data, array $context = []): Entity {
        $this->em->detach($data);
        $this->em->persist($clone = clone $data);
        $this->update($data, $clone);
        $this->em->flush();
        return $clone;
    }

    /**
     * @param Entity  $data
     * @param mixed[] $context
     */
    final public function remove($data, array $context = []): void {
    }

    /**
     * @param mixed   $data
     * @param mixed[] $context
     */
    public function supports($data, array $context = []): bool {
        return $data instanceof Entity
            && isset($context['item_operation_name'])
            && $context['item_operation_name'] === 'clone';
    }

    /**
     * @param Entity $data
     * @param Entity $clone
     */
    protected function update($data, $clone): void {
    }
}
