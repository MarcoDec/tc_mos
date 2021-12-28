<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Country;
use Symfony\Component\Intl\Countries;

final class CountryDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface {
    /**
     * @return Country[]
     */
    public function getCollection(string $resourceClass, ?string $operationName = null): array {
        return collect(Countries::getNames())
            ->map(static fn (string $name, string $code): Country => new Country($code, $name))
            ->values()
            ->all();
    }

    /**
     * @param mixed[] $context
     */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        return $resourceClass === Country::class;
    }
}
