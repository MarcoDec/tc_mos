<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Interfaces\WorkflowInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class DeleteDataPersister implements ContextAwareDataPersisterInterface {
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    /**
     * @param WorkflowInterface $data
     * @param mixed[]           $context
     */
    public function persist($data, array $context = []): void {
    }

    /**
     * @param WorkflowInterface $data
     * @param mixed[]           $context
     */
    public function remove($data, array $context = []): void {
        if ($data->isDeletable()) {
            $this->em->remove($data);
            $this->em->flush();
        } else {
            throw new BadRequestHttpException('Cette ressource n\'est pas à un statut supprimable.');
        }
    }

    /**
     * @param mixed[] $context
     */
    public function supports($data, array $context = []): bool {
        return $data instanceof WorkflowInterface
            && isset($context['item_operation_name'])
            && $context['item_operation_name'] === 'delete';
    }
}
