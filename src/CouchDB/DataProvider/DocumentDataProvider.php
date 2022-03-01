<?php

namespace App\CouchDB\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\CouchDB\DocumentManager;

final class DocumentDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface {
    public function __construct(private DocumentManager $dm) {
    }

    /**
     * @template T of \App\CouchDB\Document\Document
     *
     * @param class-string<T> $resourceClass
     *
     * @return T[]
     */
    public function getCollection(string $resourceClass, ?string $operationName = null): array {
        return $this->dm->findBy($resourceClass);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        return $this->dm->isDocument($resourceClass);
    }
}
