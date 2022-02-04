<?php

declare(strict_types=1);

namespace App\OpenApi\Factory;

use ApiPlatform\Core\Api\IdentifiersExtractorInterface;
use ApiPlatform\Core\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Core\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use ApiPlatform\Core\PathResolver\OperationPathResolverInterface;
use JetBrains\PhpStorm\Pure;

final class ItemPaths extends Paths {
    #[Pure]
    public function __construct(
        array $formats,
        private IdentifiersExtractorInterface $identifiersExtractor,
        SchemaFactoryInterface $jsonSchemaFactory,
        Links $links,
        OperationPathResolverInterface $operationPathResolver,
        PropertyMetadataFactoryInterface $propertyMetadataFactory,
        PropertyNameCollectionFactoryInterface $propertyNameCollectionFactory,
        private string $resourceClass,
        ResourceMetadata $resourceMetadata
    ) {
        parent::__construct(
            formats: $formats,
            jsonSchemaFactory: $jsonSchemaFactory,
            links: $links,
            operationPathResolver: $operationPathResolver,
            propertyMetadataFactory: $propertyMetadataFactory,
            propertyNameCollectionFactory: $propertyNameCollectionFactory,
            resourceMetadata: $resourceMetadata
        );
    }

    protected function createOperation(array $operation, string $operationName): Operation {
        return new ItemOperation(
            formats: $this->formats,
            identifiersExtractor: $this->identifiersExtractor,
            jsonSchemaFactory: $this->jsonSchemaFactory,
            links: $this->links,
            operation: $operation,
            operationName: $operationName,
            operationPathResolver: $this->operationPathResolver,
            propertyMetadataFactory: $this->propertyMetadataFactory,
            propertyNameCollectionFactory: $this->propertyNameCollectionFactory,
            resourceClass: $this->resourceClass,
            resourceMetadata: $this->resourceMetadata
        );
    }

    #[Pure]
    protected function getOperations(): array {
        return $this->resourceMetadata->getItemOperations() ?? [];
    }
}
