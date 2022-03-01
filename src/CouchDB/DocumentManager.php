<?php

namespace App\CouchDB;

use App\CouchDB\Metadata\Metadata;
use App\CouchDB\Metadata\MetadataFactory;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class DocumentManager {
    public function __construct(
        private HttpClientInterface $couchdbClient,
        private MetadataFactory $factory,
        private SerializerInterface $serializer
    ) {
    }

    /**
     * @template T of \App\Document\Document
     *
     * @param class-string<T> $class
     *
     * @return Metadata<T>
     */
    public function getMetadata(string $class): Metadata {
        return $this->factory->getMetadata($class);
    }

    public function persist(object $entity): void {
        $this->couchdbClient->request(
            method: 'POST',
            url: '_bulk_docs',
            options: ['body' => $this->serializer->serialize(['docs' => [$entity]], 'jsonld', ['groups' => 'couchdb'])]
        );
    }
}
