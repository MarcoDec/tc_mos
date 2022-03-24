<?php

namespace App\ApiPlatform\Core\OpenApi\Factory;

/**
 * @phpstan-type OpenApiProperty array{enum?: string[], oneOf?: array<mixed|SchemaContext>, readOnly: bool, type?: string}
 * @phpstan-type SchemaContext array{additionalProperties: bool, allOf?: array<array{'$ref': string}|mixed>, description?: string, type: string}
 */
interface OpenApiContext {
    /**
     * @return OpenApiProperty|SchemaContext
     */
    public function getOpenApiContext(): array;
}
