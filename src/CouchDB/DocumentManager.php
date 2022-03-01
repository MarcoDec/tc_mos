<?php

namespace App\CouchDB;

use App\CouchDB\Document\Document;
use App\CouchDB\Metadata\Metadata;
use App\CouchDB\Metadata\MetadataFactory;
use App\CouchDB\Repository\DocumentRepository;
use Exception;

final class DocumentManager {
    public function __construct(
        private CouchDBClient $couchdbClient,
        private MetadataFactory $factory,
        private DocumentRepository $repo
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

    public function persist(Document $entity): void {
        $this->couchdbClient->persist($entity);
    }
}
