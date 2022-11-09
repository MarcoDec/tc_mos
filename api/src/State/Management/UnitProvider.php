<?php

declare(strict_types=1);

namespace App\State\Management;

use ApiPlatform\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Management\Unit;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @phpstan-type Context array{operation_name?: string}
 *
 * @implements ProviderInterface<Unit>
 */
class UnitProvider implements ProviderInterface {
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    /**
     * @param array{id?: int} $uriVariables
     * @param Context $context
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Unit {
        if (empty($uriVariables['id'])) {
            throw new InvalidArgumentException('$uriVariables[\'id\'] is missing.');
        }
        $id = $uriVariables['id'];
        $repo = $this->em->getRepository(Unit::class);
        dump([
            '$context' => $context,
            '$operation' => $operation,
        ]);
        return match (true) {
            self::patch($operation, $context) => $repo->findPatch($id),
            self::eager($operation, $context) => $repo->findEager($id),
            default => $repo->find($id)
        };
    }

    /** @param Context $context */
    private static function patch(Operation $operation, array $context): bool {
        return $operation instanceof Patch
            || (
                isset($context['operation_name'])
                && $context['operation_name'] === '_api_/units/{id}{._format}_patch'
            );
    }

    /** @param Context $context */
    private static function eager(Operation $operation, array $context): bool {
        return $operation instanceof Delete
            || (
                isset($context['operation_name'])
                && str_contains($context['operation_name'], '/attributes/')
            );
    }
}
