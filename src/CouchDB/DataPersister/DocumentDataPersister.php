<?php

namespace App\CouchDB\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\CouchDB\DocumentManager;

final class DocumentDataPersister implements ContextAwareDataPersisterInterface {
    public function __construct(private DocumentManager $dm) {
    }

    /**
     * @param array<string, mixed> $context
     */
    public function persist($data, array $context = []): void {
        $this->dm->persist($context['resource_class'], $data);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function remove($data, array $context = []): void {
        // TODO: Implement remove() method.
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supports($data, array $context = []): bool {
        return $this->dm->isDocument($context['resource_class']);
    }
}
