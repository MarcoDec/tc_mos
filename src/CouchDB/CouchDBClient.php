<?php

namespace App\CouchDB;

use App\CouchDB\Document\Document;
use App\CouchDB\Repository\Finder\Finder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class CouchDBClient {
    public function __construct(private HttpClientInterface $couchdbClient, private SerializerInterface $serializer) {
    }

    public function createDatabase(): void {
        $this->couchdbClient->request('PUT', '');
    }

    /**
     * @return mixed[]
     */
    public function findBy(Finder $criteria): array {
        return $this->couchdbClient->request(
            method: 'POST',
            url: '_find',
            options: ['body' => $this->serializer->serialize($criteria, 'json')]
        )->toArray()['docs'];
    }

    public function persist(Document $entity): void {
        $this->couchdbClient->request(
            method: 'POST',
            url: '_bulk_docs',
            options: ['body' => $this->serializer->serialize(['docs' => [$entity]], 'jsonld', ['groups' => 'couchdb'])]
        );
    }
}
