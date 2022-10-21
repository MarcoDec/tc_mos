<?php

namespace App\DataProvider\Hr\Employee;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Api\Resource\Hr\Employee\EmployeeState;
use App\Collection;
use App\Doctrine\DBAL\Types\Embeddable\EmployeeEngineStateType;
use Symfony\Contracts\Translation\TranslatorInterface;

final class EmployeeStateDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface {
    public function __construct(private readonly TranslatorInterface $translator) {
    }

    /**
     * @return Collection<int, EmployeeState>
     */
    public function getCollection(string $resourceClass, ?string $operationName = null): Collection {
        return Collection::collect(EmployeeEngineStateType::TYPES)
            ->map(fn (string $value): EmployeeState => new EmployeeState(
                text: $this->translator->trans($value),
                value: $value
            ))
            ->sortBy(static fn (EmployeeState $state): string => $state->getText());
    }

    /**
     * @param mixed[] $context
     */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        return $resourceClass === EmployeeState::class;
    }
}
