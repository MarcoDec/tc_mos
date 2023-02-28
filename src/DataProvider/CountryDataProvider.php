<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Api\Resource\Country;
use App\Collection;
use Symfony\Component\Intl\Countries;

final class CountryDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface {
    /**
     * @return Collection<int, Country>
     */
    public function getCollection(string $resourceClass, ?string $operationName = null): Collection {
        return Collection::collect(Countries::getCountryCodes())
            ->map(static fn (string $code): Country => new Country($code))
            ->sortBy(static fn (Country $country): string => $country->getCode());
    }

    /**
     * @param mixed[] $context
     */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        return $resourceClass === Country::class;
    }
}
