<?php

declare(strict_types=1);

namespace App\OpenApi\Factory;

use ApiPlatform\Core\Api\IdentifiersExtractorInterface;
use ApiPlatform\Core\Api\OperationType;
use ApiPlatform\Core\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Core\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use ApiPlatform\Core\OpenApi\Model\Parameter;
use ApiPlatform\Core\OpenApi\Model\Response;
use ApiPlatform\Core\PathResolver\OperationPathResolverInterface;
use JetBrains\PhpStorm\Pure;

final class ItemOperation extends Operation {
    public function __construct(
        array $formats,
        private IdentifiersExtractorInterface $identifiersExtractor,
        SchemaFactoryInterface $jsonSchemaFactory,
        Links $links,
        array $operation,
        string $operationName,
        OperationPathResolverInterface $operationPathResolver,
        PropertyMetadataFactoryInterface $propertyMetadataFactory,
        PropertyNameCollectionFactoryInterface $propertyNameCollectionFactory,
        string $resourceClass,
        ResourceMetadata $resourceMetadata
    ) {
        parent::__construct(
            formats: $formats,
            jsonSchemaFactory: $jsonSchemaFactory,
            links: $links,
            operation: $operation,
            operationName: $operationName,
            operationPathResolver: $operationPathResolver,
            propertyMetadataFactory: $propertyMetadataFactory,
            propertyNameCollectionFactory: $propertyNameCollectionFactory,
            resourceClass: $resourceClass,
            resourceMetadata: $resourceMetadata
        );
    }

    /**
     * @param Parameter[] $parameters
     */
    #[Pure]
    private static function notHasParameter(Parameter $parameter, array $parameters): bool {
        foreach ($parameters as $existed) {
            if ($existed->getName() === $parameter->getName() && $existed->getIn() === $parameter->getIn()) {
                return false;
            }
        }
        return true;
    }

    protected function getIdentifiers(): array {
        $identifiers = $this->operation['identifiers'] ?? $this->resourceMetadata->getAttribute(
            key: 'identifiers',
            defaultValue: $this->identifiersExtractor->getIdentifiersFromResourceClass($this->resourceClass)
        );
        return (count($identifiers) > 1 ? $this->resourceMetadata->getAttribute('composite_identifier', true) : false)
            ? ['id']
            : $identifiers;
    }

    protected function getParameters(): array {
        $parameters = [];
        foreach ($this->getIdentifiers() as $name => $identifier) {
            if (self::notHasParameter($parameter = new Parameter(
                name: is_string($name) ? $name : $identifier,
                in: 'path',
                description: 'Resource identifier',
                required: true,
                deprecated: false,
                allowEmptyValue: false,
                schema: ['type' => 'string']
            ), $parameters)) {
                $parameters[] = $parameter;
            }
        }
        return $parameters;
    }

    protected function getResponseContent(): string {
        return 'resource';
    }

    protected function getResponses(): array {
        $responses = parent::getResponses();
        $responses[404] = new Response('Resource not found');
        ksort($responses);
        return $responses;
    }

    protected function getType(): string {
        return OperationType::ITEM;
    }
}
