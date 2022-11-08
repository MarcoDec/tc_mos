<?php

declare(strict_types=1);

namespace App\State\Management;

use ApiPlatform\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Management\Unit;
use Doctrine\ORM\EntityManagerInterface;

/** @implements ProviderInterface<Unit> */
class UnitProvider implements ProviderInterface {
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    /**
     * @param array{id?: int}                $uriVariables
     * @param array{operation_name?: string} $context
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Unit {
        if (empty($uriVariables['id'])) {
            throw new InvalidArgumentException('$uriVariables[\'id\'] is missing.');
        }
        return $operation instanceof Delete
        || (
            isset($context['operation_name'])
            && str_contains($context['operation_name'], '/attributes/')
        )
            ? $this->em->getRepository(Unit::class)->findEager($uriVariables['id'])
            : $this->em->getRepository(Unit::class)->find($uriVariables['id']);
    }
}
