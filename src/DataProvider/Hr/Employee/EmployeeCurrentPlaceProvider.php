<?php

namespace App\DataProvider\Hr\Employee;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Api\Resource\Hr\Employee\EmployeeCurrentPlace;
use App\Doctrine\DBAL\Types\Hr\Employee\CurrentPlaceType;
use Illuminate\Support\Collection;
use Symfony\Contracts\Translation\TranslatorInterface;

final class EmployeeCurrentPlaceProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface {
    public function __construct(private readonly TranslatorInterface $translator) {
    }

    /**
     * @return Collection<int, EmployeeCurrentPlace>
     */
    public function getCollection(string $resourceClass, ?string $operationName = null): Collection {
        return collect(CurrentPlaceType::TYPES)
            ->map(fn (string $value): EmployeeCurrentPlace => new EmployeeCurrentPlace(
                text: $this->translator->trans($value),
                value: $value
            ))
            ->values()
            ->sortBy->getText();
    }

    /**
     * @param mixed[] $context
     */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        return $resourceClass === EmployeeCurrentPlace::class;
    }
}
