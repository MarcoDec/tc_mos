<?php

namespace App\OpenApi\Factory;

use ApiPlatform\Core\DataProvider\PaginationOptions;
use ApiPlatform\Core\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Core\JsonSchema\TypeFactoryInterface;
use ApiPlatform\Core\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use ApiPlatform\Core\PathResolver\OperationPathResolverInterface;
use JetBrains\PhpStorm\Pure;
use Psr\Container\ContainerInterface;

final class CollectionPaths extends Paths {
    #[Pure]
    public function __construct(
        private ContainerInterface $filterLocator,
        array $formats,
        SchemaFactoryInterface $jsonSchemaFactory,
        private TypeFactoryInterface $jsonSchemaTypeFactory,
        Links $links,
        OperationPathResolverInterface $operationPathResolver,
        private PaginationOptions $paginationOptions,
        PropertyMetadataFactoryInterface $propertyMetadataFactory,
        PropertyNameCollectionFactoryInterface $propertyNameCollectionFactory,
        string $resourceClass,
        ResourceMetadata $resourceMetadata
    ) {
        parent::__construct(
            formats: $formats,
            jsonSchemaFactory: $jsonSchemaFactory,
            links: $links,
            operationPathResolver: $operationPathResolver,
            propertyMetadataFactory: $propertyMetadataFactory,
            propertyNameCollectionFactory: $propertyNameCollectionFactory,
            resourceClass: $resourceClass,
            resourceMetadata: $resourceMetadata
        );
    }

    protected function createOperation(array $operation, string $operationName): Operation {
        return new CollectionOperation(
            filterLocator: $this->filterLocator,
            formats: $this->formats,
            jsonSchemaFactory: $this->jsonSchemaFactory,
            jsonSchemaTypeFactory: $this->jsonSchemaTypeFactory,
            links: $this->links,
            operation: $operation,
            operationName: $operationName,
            operationPathResolver: $this->operationPathResolver,
            paginationOptions: $this->paginationOptions,
            propertyMetadataFactory: $this->propertyMetadataFactory,
            propertyNameCollectionFactory: $this->propertyNameCollectionFactory,
            resourceClass: $this->resourceClass,
            resourceMetadata: $this->resourceMetadata
        );
    }

    #[Pure]
    protected function getOperations(): array {
        return $this->resourceMetadata->getCollectionOperations() ?? [];
    }
}
