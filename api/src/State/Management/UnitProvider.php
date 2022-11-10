<?php

declare(strict_types=1);

namespace App\State\Management;

use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Management\Unit;
use Doctrine\ORM\EntityManagerInterface;

/** @implements ProviderInterface<Unit> */
class UnitProvider implements ProviderInterface {
    /** @var string[] */
    private const ROUTES = ['_api_/attributes/{id}{._format}_patch', '_api_/attributes{._format}_post'];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ItemProvider $provider
    ) {
    }

    /**
     * @param array{id?: int}                $uriVariables
     * @param array{operation_name?: string} $context
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Unit {
        if (empty($uriVariables['id'])) {
            throw new InvalidArgumentException('$uriVariables[\'id\'] is missing.');
        }
        /* @phpstan-ignore-next-line */
        return $operation instanceof Delete || (
            isset($context['operation_name'])
            && in_array($context['operation_name'], self::ROUTES, true)
        )
            ? $this->em->getRepository(Unit::class)->findEager($uriVariables['id'])
            : $this->provider->provide($operation, $uriVariables, $context);
    }
}
