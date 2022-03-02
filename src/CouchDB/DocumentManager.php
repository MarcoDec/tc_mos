<?php

namespace App\CouchDB;

use App\CouchDB\Document\Document;
use App\CouchDB\Metadata\Metadata;
use App\CouchDB\Metadata\MetadataFactory;
use App\CouchDB\Repository\DocumentRepository;
use App\CouchDB\Repository\Finder\Finder;
use Exception;

final class DocumentManager {
    public function __construct(
        private CouchDBClient $couchdbClient,
        private MetadataFactory $factory,
        private DocumentRepository $repo
    ) {
    }

    public function createDatabase(): void {
        $this->couchdbClient->createDatabase();
    }

    /**
     * @template T of \App\CouchDB\Document\Document
     *
     * @param class-string<T> $class
     *
     * @return T[]
     */
    public function findBy(string $class, ?Finder $criteria = null): array {
        return $this->repo->findBy($class, $criteria);
    }

    /**
     * @template T of \App\CouchDB\Document\Document
     *
     * @param class-string<T> $class
     *
     * @return Metadata<T>
     */
    public function getMetadata(string $class): Metadata {
        return $this->factory->getMetadata($class);
    }

    public function isDocument(string $class): bool {
        try {
            /** @phpstan-ignore-next-line */
            $this->getMetadata($class);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @template T of \App\CouchDB\Document\Document
     *
     * @param class-string<T> $class
     */
    public function persist(string $class, Document $entity): void {
        $this->repo->persist($class, $entity);
    }
}
