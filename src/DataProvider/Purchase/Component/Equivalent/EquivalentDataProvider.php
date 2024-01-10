<?php

namespace App\DataProvider\Purchase\Component\Equivalent;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Purchase\Component\Equivalent\ComponentEquivalent;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\NotSupported;

class EquivalentDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     */
    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $loadedItem = $this->em->getRepository($resourceClass)->find($id);
        dump($loadedItem->getComponents()->getValues());
        return $loadedItem;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === ComponentEquivalent::class && in_array($operationName, ['get', 'patch']);
    }
}