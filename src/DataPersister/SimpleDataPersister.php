<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Logistics\Stock\Stock;
use App\Entity\Project\Product\Product;
use App\Entity\Purchase\Component\Component;
use Doctrine\ORM\EntityManagerInterface;

final class SimpleDataPersister implements ContextAwareDataPersisterInterface {
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    /**
     * @param Stock<Component|Product> $data
     * @param mixed[]                  $context
     *
     * @return Stock<Component|Product>
     */
    public function persist($data, array $context = []): Stock {
        $this->em->persist($data);
        $this->em->flush();
        return $data;
    }

    /**
     * @param Stock<Component|Product> $data
     * @param mixed[]                  $context
     */
    public function remove($data, array $context = []): void {
    }

    /**
     * @param mixed[] $context
     */
    public function supports($data, array $context = []): bool {
        return $data instanceof Stock
            && isset($context['collection_operation_name'])
            && in_array($context['collection_operation_name'], ['post', 'receipt']);
    }
}
