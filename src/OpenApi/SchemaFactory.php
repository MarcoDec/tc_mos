<?php

namespace App\OpenApi;

use ApiPlatform\Core\Exception\ResourceClassNotFoundException;
use ApiPlatform\Core\JsonSchema\Schema;
use ApiPlatform\Core\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Operation\DashPathSegmentNameGenerator;

final class SchemaFactory implements SchemaFactoryInterface {
    public function __construct(
        private DashPathSegmentNameGenerator $dashGenerator,
        private SchemaFactoryInterface $decorated,
        private ResourceMetadataFactoryInterface $resourceMetadataFactory
    ) {
    }

    public function buildSchema(
        string $className,
        string $format = 'json',
        string $type = Schema::TYPE_OUTPUT,
        ?string $operationType = null,
        ?string $operationName = null,
        ?Schema $schema = null,
        ?array $serializerContext = null,
        bool $forceCollection = false
    ): Schema {
        $schema = $this->decorated->buildSchema(
            className: $className,
            format: $format,
            type: $type,
            operationType: $operationType,
            operationName: $operationName,
            schema: $schema,
            serializerContext: $serializerContext,
            forceCollection: $forceCollection
        );
        if ('jsonld' !== $format) {
            return $schema;
        }

        $definitions = $schema->getDefinitions();

        if ($key = $schema->getRootDefinitionKey()) {
            try {
                $resourceName = $this->resourceMetadataFactory->create($className)->getShortName();
            } catch (ResourceClassNotFoundException $e) {
                return $schema;
            }
            $definitions[$key]['properties']['@context']['example'] = "/api/contexts/$resourceName";
            $definitions[$key]['properties']['@id']['example'] = "/api/{$this->dashGenerator->getSegmentName($resourceName)}/1";
            $definitions[$key]['properties']['@type']['example'] = $resourceName;
        }

        return $schema;
    }
}
