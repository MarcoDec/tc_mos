<?php

namespace App\CouchDB\Repository;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use App\CouchDB\CouchDBClient;
use App\CouchDB\Document\Document;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class DocumentRepository {
    public function __construct(
        private CouchDBClient $client,
        private DenormalizerInterface $denormalizer,
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
        return collect($this->client->findBy($criteria))
            ->map(fn (array $doc): Document => $this->denormalizer->denormalize($doc, $class))
            ->values()
            ->all();
    }
}
