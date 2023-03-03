<?php

namespace App\DataPersister\Purchase\Component;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Purchase\Component\Attribute;
use App\Entity\Purchase\Component\ComponentAttribute;
use Doctrine\ORM\EntityManagerInterface;

final class AttributeDataPersister implements ContextAwareDataPersisterInterface {
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    /**
     * @param Attribute $data
     * @param mixed[]   $context
     */
    public function persist($data, array $context = []): Attribute {
        $this->em->beginTransaction();
        $this->em->persist($data);
        $this->em->flush();
        $this->em->getRepository(ComponentAttribute::class)->links();
        $this->em->commit();
        return $data;
    }

    /**
     * @param Attribute $data
     * @param mixed[]   $context
     */
    public function remove($data, array $context = []): void {
    }

    /**
     * @param mixed[] $context
     */
    public function supports($data, array $context = []): bool {
        return $data instanceof Attribute
            && (
                (isset($context['collection_operation_name']) && $context['collection_operation_name'] === 'post')
                || (isset($context['item_operation_name']) && $context['item_operation_name'] === 'patch')
            );
    }
}
