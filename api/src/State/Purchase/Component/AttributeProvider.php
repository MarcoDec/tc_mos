<?php

declare(strict_types=1);

namespace App\State\Purchase\Component;

use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Purchase\Component\Attribute;
use Doctrine\ORM\EntityManagerInterface;

/** @implements ProviderInterface<Attribute> */
class AttributeProvider implements ProviderInterface {
    public function __construct(private readonly EntityManagerInterface $em, private readonly ItemProvider $provider) {
    }

    /**
     * @param mixed[]                        $uriVariables
     * @param array{operation_name?: string} $context
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Attribute {
        /* @phpstan-ignore-next-line */
        return $operation instanceof Patch || (
            isset($context['operation_name'])
            && $context['operation_name'] === '_api_/component-families/{id}{._format}_patch'
        )
            ? $this->em->getRepository(Attribute::class)->provideItem($operation, $uriVariables, $context)
            : $this->provider->provide($operation, $uriVariables, $context);
    }
}
