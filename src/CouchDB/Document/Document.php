<?php

namespace App\CouchDB\Document;

abstract class Document {
    private string $_id = 'Document-0';
    private int $id = 0;

    final public function get_Id(): string {
        return $this->_id;
    }

    final public function getId(): int {
        return $this->id;
    }

    final public function setIdentifiers(int $id, string $shortName): self {
        $this->id = $id;
        $this->_id = "$shortName-$id";
        return $this;
    }
}
