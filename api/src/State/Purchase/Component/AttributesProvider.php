<?php

declare(strict_types=1);

namespace App\State\Purchase\Component;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Purchase\Component\Attribute;
use Doctrine\ORM\EntityManagerInterface;

/** @implements ProviderInterface<Attribute> */
class AttributesProvider implements ProviderInterface {
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    /**
     * @param  mixed[]                          $uriVariables
     * @param  mixed[]                          $context
     * @return Attribute[]|Paginator<Attribute>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|Paginator {
        return $this->em->getRepository(Attribute::class)->provide($operation, $uriVariables, $context);
    }
}
