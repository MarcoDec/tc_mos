<?php

namespace App\CouchDB;

use App\CouchDB\Document\Document;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class CouchDBClient {
    public function __construct(private HttpClientInterface $couchdbClient, private SerializerInterface $serializer) {
    }

    /**
     * @param array<string, mixed> $criteria
     *
     * @return Document[]
     */
    public function findBy(array $criteria): array {
        return $this->couchdbClient->request(
            method: 'POST',
            url: '_find',
            options: ['body' => json_encode(['selector' => $criteria])]
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
