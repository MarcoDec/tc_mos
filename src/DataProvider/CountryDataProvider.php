<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Api\Resource\Country;
use Illuminate\Support\Collection;
use Symfony\Component\Intl\Countries;

final class CountryDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface {
    /**
     * @return Collection<int, Country>
     */
    public function getCollection(string $resourceClass, ?string $operationName = null): Collection {
        return collect(Countries::getCountryCodes())
            ->map(static fn (string $code): Country => new Country($code))
            ->values()
            ->sortBy->getName();
    }

    /**
     * @param mixed[] $context
     */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        return $resourceClass === Country::class;
    }
}
