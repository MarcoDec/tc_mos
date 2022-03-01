<?php

namespace App\Doctrine\DataPersister\Project\Product\Product;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Doctrine\Entity\Project\Product\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class DeleteDataPersister implements ContextAwareDataPersisterInterface {
    public function __construct(private EntityManagerInterface $em) {
    }

    /**
     * @param Product $data
     * @param mixed[] $context
     */
    public function persist($data, array $context = []): void {
    }

    /**
     * @param Product $data
     * @param mixed[] $context
     */
    public function remove($data, array $context = []): void {
        if ($data->isDeletable()) {
            $this->em->remove($data);
            $this->em->flush();
        } else {
            throw new BadRequestHttpException('Ce produit n\'est pas à un statut supprimable.');
        }
    }

    /**
     * @param mixed[] $context
     */
    public function supports($data, array $context = []): bool {
        return $data instanceof Product
            && isset($context['item_operation_name'])
            && $context['item_operation_name'] === 'delete';
    }
}
