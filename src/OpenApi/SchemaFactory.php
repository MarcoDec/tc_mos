<?php

namespace App\OpenApi;

use ApiPlatform\Core\Exception\ResourceClassNotFoundException;
use ApiPlatform\Core\JsonSchema\Schema;
use ApiPlatform\Core\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Operation\DashPathSegmentNameGenerator;
use App\Entity\Embeddable\Measure;

final class SchemaFactory implements SchemaFactoryInterface {
    public function __construct(
        private DashPathSegmentNameGenerator $dashGenerator,
        private SchemaFactoryInterface $decorated,
        private ResourceMetadataFactoryInterface $resourceMetadataFactory
    ) {
    }

    /**
     * @param null|Schema<string, mixed> $schema
     * @param mixed[]|null               $serializerContext
     *
     * @return Schema<string, mixed>
     */
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

        $definitions = $schema->getDefinitions();
        $key = $schema->getRootDefinitionKey();

        if ($className === Measure::class && !empty($key)) {
            unset($definitions[$key]);
            return $schema;
        }

        if ('jsonld' !== $format) {
            return $schema;
        }

        if (!empty($key)) {
            try {
                $resourceName = $this->resourceMetadataFactory->create($className)->getShortName();
            } catch (ResourceClassNotFoundException $e) {
                return $schema;
            }
            if (!empty($resourceName)) {
                $definitions[$key]['properties']['@context']['example'] = "/api/contexts/$resourceName";
                $definitions[$key]['properties']['@id']['example'] = "/api/{$this->dashGenerator->getSegmentName($resourceName)}/1";
                $definitions[$key]['properties']['@type']['example'] = $resourceName;
            }
        }

        return $schema;
    }
}
