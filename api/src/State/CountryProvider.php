<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Country;
use App\Collection;
use Symfony\Component\Intl\Countries;

/** @implements ProviderInterface<Country> */
class CountryProvider implements ProviderInterface {
    /**
     * @param  mixed[]   $uriVariables
     * @param  mixed[]   $context
     * @return Country[]
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array {
        return (new Collection(Countries::getCountryCodes()))
            ->map(static fn (string $code): Country => new Country($code))
            ->sortBy(static fn (Country $a, Country $b): int => ($cmp = strcmp($b->getGroup(), $a->getGroup())) !== 0 ? $cmp : strcmp($a->getCode(), $b->getCode()))
            ->toArray();
    }
}
