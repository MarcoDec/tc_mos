<?php

namespace App\OpenApi\Factory;

use ApiPlatform\Core\Api\FilterInterface;
use ApiPlatform\Core\Api\OperationType;
use ApiPlatform\Core\DataProvider\PaginationOptions;
use ApiPlatform\Core\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Core\JsonSchema\TypeFactoryInterface;
use ApiPlatform\Core\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use ApiPlatform\Core\OpenApi\Model\Parameter;
use ApiPlatform\Core\PathResolver\OperationPathResolverInterface;
use ArrayObject;
use function in_array;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\PropertyInfo\Type;

final class CollectionOperation extends Operation {
    public function __construct(
        private ContainerInterface $filterLocator,
        array $formats,
        SchemaFactoryInterface $jsonSchemaFactory,
        private TypeFactoryInterface $jsonSchemaTypeFactory,
        Links $links,
        array $operation,
        string $operationName,
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
            operation: $operation,
            operationName: $operationName,
            operationPathResolver: $operationPathResolver,
            propertyMetadataFactory: $propertyMetadataFactory,
            propertyNameCollectionFactory: $propertyNameCollectionFactory,
            resourceClass: $resourceClass,
            resourceMetadata: $resourceMetadata,
        );
    }

    protected function getIdentifiers(): array {
        return [];
    }

    protected function getParameters(): array {
        $parameters = [];
        if ($this->getMethod() === 'GET') {
            return $parameters;
        }
        foreach (array_merge($this->getPaginationParameters(), $this->getFiltersParameters()) as $parameter) {
            if (self::notHasParameter($parameter, $parameters)) {
                $parameters[] = $parameter;
            }
        }
        return $parameters;
    }

    protected function getResponseContent(): string {
        return 'collection';
    }

    protected function getType(): string {
        return OperationType::COLLECTION;
    }

    private function getFilter(string $filterId): ?FilterInterface {
        try {
            return $this->filterLocator->get($filterId);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            return null;
        }
    }

    /**
     * @return Parameter[]
     */
    private function getFiltersParameters(): array {
        $parameters = [];
        $resourceFilters = $this->resourceMetadata->getCollectionOperationAttribute(
            operationName: $this->operationName,
            key: 'filters',
            defaultValue: [],
            resourceFallback: true
        );
        foreach ($resourceFilters as $filterId) {
            if (!empty($filter = $this->getFilter($filterId))) {
                foreach ($filter->getDescription($this->resourceClass) as $name => $data) {
                    $schema = $data['schema'] ?? (
                        in_array($data['type'], Type::$builtinTypes, true)
                            ? $this->jsonSchemaTypeFactory->getType(new Type(
                                builtinType: $data['type'],
                                nullable: false,
                                class: null,
                                collection: $data['is_collection'] ?? false
                            ))
                            : ['type' => 'string']
                    );
                    $parameters[] = new Parameter(
                        name: $name,
                        in: 'query',
                        description: $data['description'] ?? '',
                        required: $data['required'] ?? false,
                        deprecated: $data['openapi']['deprecated'] ?? false,
                        allowEmptyValue: $data['openapi']['allowEmptyValue'] ?? true,
                        schema: $schema,
                        style: 'array' === $schema['type']
                        && in_array($data['type'], [Type::BUILTIN_TYPE_ARRAY, Type::BUILTIN_TYPE_OBJECT], true)
                            ? 'deepObject'
                            : 'form',
                        explode: $data['openapi']['explode'] ?? ('array' === $schema['type']),
                        allowReserved: $data['openapi']['allowReserved'] ?? false,
                        example: $data['openapi']['example'] ?? null,
                        examples: isset($data['openapi']['examples'])
                            ? new ArrayObject($data['openapi']['examples'])
                            : null
                    );
                }
            }
        }
        return $parameters;
    }

    /**
     * @return Parameter[]
     */
    private function getPaginationParameters(): array {
        if (!$this->paginationOptions->isPaginationEnabled()) {
            return [];
        }
        $parameters = [];
        if ($this->resourceMetadata->getCollectionOperationAttribute(
            operationName: $this->operationName,
            key: 'pagination_enabled',
            defaultValue: true,
            resourceFallback: true
        )) {
            $parameters[] = new Parameter(
                name: $this->paginationOptions->getPaginationPageParameterName(),
                in: 'query',
                description: 'The collection page number',
                required: false,
                deprecated: false,
                allowEmptyValue: true,
                schema: ['type' => 'integer', 'default' => 1]
            );
            if ($this->resourceMetadata->getCollectionOperationAttribute(
                operationName: $this->operationName,
                key: 'pagination_client_items_per_page',
                defaultValue: $this->paginationOptions->getClientItemsPerPage(),
                resourceFallback: true
            )) {
                $schema = [
                    'type' => 'integer',
                    'default' => $this->resourceMetadata->getCollectionOperationAttribute(
                        operationName: $this->operationName,
                        key: 'pagination_items_per_page',
                        defaultValue: 30,
                        resourceFallback: true
                    ),
                    'minimum' => 0,
                ];
                if (null !== $maxItemsPerPage = $this->resourceMetadata->getCollectionOperationAttribute(
                    operationName: $this->operationName,
                    key: 'pagination_maximum_items_per_page',
                    defaultValue: null,
                    resourceFallback: true
                )
                ) {
                    $schema['maximum'] = $maxItemsPerPage;
                }
                $parameters[] = new Parameter(
                    name: $this->paginationOptions->getItemsPerPageParameterName(),
                    in: 'query',
                    description: 'The number of items per page',
                    required: false,
                    deprecated: false,
                    allowEmptyValue: true,
                    schema: $schema
                );
            }
        }
        if ($this->resourceMetadata->getCollectionOperationAttribute(
            operationName: $this->operationName,
            key: 'pagination_client_enabled',
            defaultValue: $this->paginationOptions->getPaginationClientEnabled(),
            resourceFallback: true
        )) {
            $parameters[] = new Parameter(
                name: $this->paginationOptions->getPaginationClientEnabledParameterName(),
                in: 'query',
                description: 'Enable or disable pagination',
                required: false,
                deprecated: false,
                allowEmptyValue: true,
                schema: ['type' => 'boolean']
            );
        }
        return $parameters;
    }
}
