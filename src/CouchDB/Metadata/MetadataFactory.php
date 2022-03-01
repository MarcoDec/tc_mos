<?php

namespace App\CouchDB\Metadata;

use App\CouchDB\Document\Document;
use InvalidArgumentException;
use ReflectionClass;

final class MetadataFactory {
    /**
     * @template T of \App\CouchDB\Document\Document
     *
     * @param class-string<T> $class
     *
     * @return Metadata<T>
     */
    public function getMetadata(string $class): Metadata {
        $refl = new ReflectionClass($class);
        if ($refl->isSubclassOf(Document::class)) {
            return new Metadata($refl);
        }
        throw new InvalidArgumentException("Class \"$class\" is not a document.");
    }
}
