<?php

namespace App\CouchDB\Metadata;

use JetBrains\PhpStorm\Pure;
use ReflectionClass;

/**
 * @template T of \App\Document\Document
 */
final class Metadata {
    /**
     * @param ReflectionClass<T> $refl
     */
    public function __construct(private ReflectionClass $refl) {
    }

    #[Pure]
    public function getTypeOfField(string $name): string {
        $type = $this->refl->getProperty($name)->getType();
        return $type !== null && method_exists($type, 'getName') ? $type->getName() : 'null';
    }
}
