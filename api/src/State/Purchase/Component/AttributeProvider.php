<?php

declare(strict_types=1);

namespace App\State\Purchase\Component;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Purchase\Component\Attribute;
use Doctrine\ORM\EntityManagerInterface;

/** @implements ProviderInterface<Attribute> */
class AttributeProvider implements ProviderInterface {
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    /**
     * @param mixed[]                  $uriVariables
     * @param array{fetch_data?: bool} $context
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Attribute {
        return $this->em->getRepository(Attribute::class)->provideItem($operation, $uriVariables, $context);
    }
}
