<?php

namespace App\Entity\Couchdb;

/**
 * Représente une propriété d'un item d'un document CouchDB.
 */
class Property {
    private bool $loaded;
    private string $name;
    private object|null $object;
    private string $type;

    public function __construct() {
        $this->loaded = false;
        $this->object = null;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getObject(): ?object {
        return $this->object;
    }

    public function getType(): string {
        return $this->type;
    }

    public function isLoaded(): bool {
        return $this->loaded;
    }

    public function setLoaded(bool $loaded): void {
        $this->loaded = $loaded;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setObject(?object $object): void {
        $this->object = $object;
    }

    public function setType(string $type): void {
        $this->type = $type;
    }
}
