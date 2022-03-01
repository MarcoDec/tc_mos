<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\CouchDB\DocumentManager;
use Exception;

final class DocumentDataPersister implements ContextAwareDataPersisterInterface {
    public function __construct(private DocumentManager $dm) {
    }

    public function persist($data, array $context = []): void {
        $this->dm->persist($data);
    }

    public function remove($data, array $context = []): void {
        // TODO: Implement remove() method.
    }

    public function supports($data, array $context = []): bool {
        try {
            $this->dm->getMetadata($context['resource_class']);
            return true;
        } catch (Exception$e) {
            return false;
        }
    }
}
