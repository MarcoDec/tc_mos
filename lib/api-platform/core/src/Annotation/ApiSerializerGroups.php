<?php

namespace App\ApiPlatform\Core\Annotation;

use Attribute;

#[Attribute]
final class ApiSerializerGroups {
    /**
     * @param array<string, string[]> $inheritedRead
     * @param string[]                $write
     */
    public function __construct(
        public readonly array $inheritedRead = [],
        public readonly array $write = []
    ) {
    }
}
