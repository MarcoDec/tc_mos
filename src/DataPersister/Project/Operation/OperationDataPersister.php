<?php

namespace App\DataPersister\Project\Operation;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Project\Operation\Operation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class OperationDataPersister implements ContextAwareDataPersisterInterface {
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    /**
     * @param mixed[] $context
     */
    public function persist($data, array $context = []): void {
    }

    /**
     * @param Operation $data
     * @param mixed[]   $context
     */
    public function remove($data, array $context = []): void {
        if (($count = $data->getCountOperations()) > 0) {
            throw new UnprocessableEntityHttpException("Cette opération a été effectuée {$count} fois. Vous ne pouvez supprimer que les opérations qui n'ont jamais été effectuée.");
        }
        $this->em->remove($data);
        $this->em->flush();
    }

    /**
     * @param mixed[] $context
     */
    public function supports($data, array $context = []): bool {
        return $data instanceof Operation
            && isset($context['item_operation_name'])
            && $context['item_operation_name'] === 'delete';
    }
}
