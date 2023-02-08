<?php

namespace App\DataProvider\Hr\Employee;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Api\Resource\Hr\Employee\EmployeeState;
use App\Collection;
use App\Doctrine\DBAL\Types\Embeddable\Hr\Event\EventStateType;
use Symfony\Contracts\Translation\TranslatorInterface;

final class EmployeeStateDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface {
    public function __construct(private readonly TranslatorInterface $translator) {
    }

    /** @return Collection<int, EmployeeState> */
    public function getCollection(string $resourceClass, ?string $operationName = null): Collection {
        return Collection::collect(EventStateType::TYPES)
            ->map(fn (string $id): EmployeeState => new EmployeeState(
                id: $id,
                text: $this->translator->trans($id)
            ))
            ->sortBy(static fn (EmployeeState $state): string => $state->getText());
    }

    /** @param mixed[] $context */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        return $resourceClass === EmployeeState::class;
    }
}
