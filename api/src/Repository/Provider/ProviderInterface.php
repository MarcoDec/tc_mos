<?php

declare(strict_types=1);

namespace App\Repository\Provider;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Metadata\Operation;

/** @template T of \App\Entity\Entity */
interface ProviderInterface {
    /**
     * @param  mixed[]          $uriVariables
     * @param  mixed[]          $context
     * @return Paginator<T>|T[]
     */
    public function provideCollection(Operation $operation, array $uriVariables = [], array $context = []): array|Paginator;

    /**
     * @param  mixed[]                  $uriVariables
     * @param  array{fetch_data?: bool} $context
     * @return null|T
     */
    public function provideItem(Operation $operation, array $uriVariables = [], array $context = []): mixed;
}
