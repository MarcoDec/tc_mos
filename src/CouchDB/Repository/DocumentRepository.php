<?php

namespace App\CouchDB\Repository;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use App\CouchDB\CouchDBClient;
use App\CouchDB\Document\Document;
use App\CouchDB\Exception\NonUniqueResultException;
use App\CouchDB\Repository\Finder\Finder;
use App\CouchDB\Repository\Finder\Selector;
use InvalidArgumentException;
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
     * @param class-string<T> $class
     *
     * @return T[]
     */
    public function findBy(string $class, ?Finder $criteria = null): array {
        if ($criteria === null) {
            $criteria = new Finder();
        }
        $criteria->where('@type', $this->getShortName($class));
        /** @phpstan-ignore-next-line */
        return collect($this->client->findBy($criteria))
            ->map(fn (array $doc): Document => $this->denormalizer->denormalize($doc, $class))
            ->values()
            ->all();
    }

    /**
     * @template T of \App\CouchDB\Document\Document
     *
     * @param class-string<T> $class
     */
    public function getShortName(string $class): string {
        if (empty($shortName = $this->resourceFactory->create($class)->getShortName())) {
            throw new InvalidArgumentException('ShortName not found.');
        }
        return $shortName;
    }

    /**
     * @template T of \App\CouchDB\Document\Document
     *
     * @param class-string<T> $class
     */
    public function lastId(string $class): int {
        $response = $this->client->findBy(new Finder(
            fields: ['id'],
            limit: 1,
            selector: (new Selector(['@type' => $this->getShortName($class)]))->greaterThan('id')
        ));
        return match (count($response)) {
            0 => 0,
            1 => $response[0]['id'],
            default => throw new NonUniqueResultException()
        };
    }

    /**
     * @template T of \App\CouchDB\Document\Document
     *
     * @param class-string<T> $class
     */
    public function nextId(string $class): int {
        return $this->lastId($class) + 1;
    }

    /**
     * @template T of \App\CouchDB\Document\Document
     *
     * @param class-string<T> $class
     */
    public function persist(string $class, Document $entity): void {
        $this->client->persist($entity->setIdentifiers($this->nextId($class), $this->getShortName($class)));
    }
}
