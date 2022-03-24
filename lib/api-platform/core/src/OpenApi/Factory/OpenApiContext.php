<?php

namespace App\ApiPlatform\Core\OpenApi\Factory;

/**
 * @phpstan-type OpenApiProperty array{default?: mixed, enum?: string[], example?: mixed, externalDocs?: array{url: string}, format?: string, maxLength?: int, minLength?: int, nullable: bool, oneOf?: array<mixed|SchemaContext>, readOnly: bool, type?: string}
 * @phpstan-type SchemaContext array{additionalProperties: bool, allOf?: array<array{'$ref': string}|mixed>, description?: string, nullable?: bool, type: string}
 */
interface OpenApiContext {
    /**
     * @return OpenApiProperty|SchemaContext
     */
    public function getOpenApiContext(): array;
}
