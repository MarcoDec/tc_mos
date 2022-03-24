<?php

namespace App\ApiPlatform\Core\Annotation;

use Attribute;

#[Attribute]
final class ApiSerializerGroups {
    /**
     * @param array<string, string[]> $inheritedRead
     */
    public function __construct(public readonly array $inheritedRead = []) {
    }
}
