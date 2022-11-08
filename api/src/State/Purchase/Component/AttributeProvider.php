<?php

declare(strict_types=1);

namespace App\State\Purchase\Component;

use ApiPlatform\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Purchase\Component\Attribute;
use Doctrine\ORM\EntityManagerInterface;

/** @implements ProviderInterface<Attribute> */
class AttributeProvider implements ProviderInterface {
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    /**
     * @param array{id?: int} $uriVariables
     * @param mixed[]         $context
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Attribute {
        if (empty($uriVariables['id'])) {
            throw new InvalidArgumentException('$uriVariables[\'id\'] is missing.');
        }
        return $this->em->getRepository(Attribute::class)->findEager($uriVariables['id']);
    }
}
