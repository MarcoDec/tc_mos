<?php

namespace App\CouchDB\Document;

use Symfony\Component\Serializer\Annotation as Serializer;

abstract class Document {
    private int $id = 0;

    #[Serializer\Groups(['couchdb'])]
    final public function getId(): int {
        return $this->id;
    }
}
