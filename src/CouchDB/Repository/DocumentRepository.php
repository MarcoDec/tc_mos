<?php

namespace App\CouchDB\Repository;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use App\CouchDB\CouchDBClient;

final class DocumentRepository {
    public function __construct(
        private CouchDBClient $client,
        private ResourceMetadataFactoryInterface $resourceFactory
    ) {
    }

    /**
     * @template T of \App\CouchDB\Document\Document
     *
     * @param class-string<T>      $class
     * @param array<string, mixed> $criteria
     *
     * @return T[]
     */
    public function findBy(string $class, array $criteria = []): array {
        $criteria['@type'] = $this->resourceFactory->create($class)->getShortName();
        /** @phpstan-ignore-next-line */
        return $this->client->findBy($criteria);
    }
}
