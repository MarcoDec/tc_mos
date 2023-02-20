<?php

namespace App\DataProvider\Management;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Management\Printer;
use App\Repository\Management\PrinterRepository;

final class PrinterDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface {
    public function __construct(private readonly PrinterRepository $repo) {
    }

    /**
     * @return Printer[]
     */
    public function getCollection(string $resourceClass, ?string $operationName = null): array {
        return $this->repo->findAll();
    }

    /**
     * @param mixed[] $context
     */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        return $resourceClass === Printer::class && $operationName === 'get';
    }
}
