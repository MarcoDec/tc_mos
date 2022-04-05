<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\Api\FilterInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Psr\Container\ContainerInterface;

trait FilterLocatorTrait {
    private ?ContainerInterface $filterLocator = null;

    private function getFilter(string $filterId): ?FilterInterface {
        return $this->filterLocator instanceof ContainerInterface && $this->filterLocator->has($filterId)
            ? $this->filterLocator->get($filterId)
            : null;
    }

    private function setFilterLocator(?ContainerInterface $filterLocator, bool $allowNull = false): void {
        if ($filterLocator === null && !$allowNull) {
            throw new InvalidArgumentException('Null $filterLocator not allowed.');
        }
        $this->filterLocator = $filterLocator;
    }
}
