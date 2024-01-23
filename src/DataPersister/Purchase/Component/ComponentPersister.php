<?php

namespace App\DataPersister\Purchase\Component;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Purchase\Component\Component;
use App\Entity\Purchase\Component\Family;
use Doctrine\ORM\EntityManagerInterface;

class ComponentPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Component
            && (
            (isset($context['collection_operation_name'])
            && $context['collection_operation_name'] === 'post')
            || (isset($context['item_operation_name'])
            && $context['item_operation_name'] === 'patch')
            );
    }
    public function persist($data, array $context = [])
    {
        /** @var Component $data */
        if ($data->getFamily() !== null) {
            $family = $this->em->getRepository(Family::class)->find($data->getFamily()->getId());
            $data->setCode($family->getCode().'-'.$data->getId());
            $data->setCustomsCode($family->getCustomsCode());
            $this->em->persist($data);
            $this->em->flush();
        }
    }

    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
    }
}