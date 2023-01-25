<?php

declare(strict_types=1);

namespace App\State\Purchase\Component;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Purchase\Component\Family;
use Doctrine\ORM\EntityManagerInterface;

/** @implements ProviderInterface<Family> */
class FamilyProvider implements ProviderInterface {
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    /**
     * @param mixed[]                  $uriVariables
     * @param array{fetch_data?: bool} $context
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Family {
        return $this->em->getRepository(Family::class)->provideItem($operation, $uriVariables, $context);
    }
}
