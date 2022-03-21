<?php

namespace App\ApiPlatform\Core\OpenApi\Factory;

use ApiPlatform\Core\Api\OperationType;
use ApiPlatform\Core\Exception\ResourceClassNotFoundException;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Options;
use ApiPlatform\Core\PathResolver\OperationPathResolverInterface;
use App\ApiPlatform\Core\Annotation\ApiProperty;
use App\ApiPlatform\Core\Annotation\ApiSerializerGroups;
use App\Service\EntitiesReflectionClassCollector;
use ArrayObject;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @phpstan-import-type SchemaContext from Schema
 *
 * @phpstan-type Operation array{method: string, openapi_context: OperationContext}
 * @phpstan-type OperationContext array{description: string, summary: string}
 */
final class OpenApiFactory implements OpenApiFactoryInterface {
    public function __construct(
        private readonly EntitiesReflectionClassCollector $classCollector,
        private readonly ResourceMetadataFactoryInterface $metadataFactory,
        private readonly OperationPathResolverInterface $resolver,
        private readonly Options $options
    ) {
    }

    /**
     * @param array{base_url?: string} $context
     */
    public function __invoke(array $context = []): OpenApi {
        $openApi = $this->generateOpenApi();
        return new OpenApi(
            info: $this->getInfo(),
            servers: [],
            paths: $openApi['paths'],
            components: new Model\Components(
                schemas: $openApi['schemas'],
                responses: $openApi['responses'],
                parameters: new ArrayObject([
                    'id' => new Model\Parameter(
                        name: 'id',
                        in: 'path',
                        required: true,
                        schema: ['type' => 'integer'],
                        example: 1
                    )
                ]),
                requestBodies: $openApi['bodies']
            )
        );
    }

    /**
     * @param ReflectionClass<T> $refl
     *
     * @template T of object
     */
    private static function getReflDescription(ReflectionClass $refl): ?string {
        if (!empty($doc = $refl->getDocComment())) {
            $matches = [];
            preg_match('/^\/\*\*\n\s\*\s(.*)\n\s\*\//m', $doc, $matches);
            return isset($matches[1]) && !empty($matches[1]) ? $matches[1] : null;
        }
        return null;
    }

    /**
     * @return Collection<string, Schema>
     */
    private function createSchemas(): Collection {
        return collect(['Resource' => new Schema(
            description: 'Base d\'une resource.',
            properties: [
                '@context' => new ApiProperty(
                    oneOf: [
                        new ApiProperty(nullable: false),
                        new Schema(
                            additionalProperties: true,
                            nullable: false,
                            properties: [
                                '@vocab' => new ApiProperty(nullable: false, readOnly: true, required: true),
                                'hydra' => new ApiProperty(
                                    enum: ['https://www.w3.org/ns/hydra/core'],
                                    nullable: false,
                                    readOnly: true,
                                    required: true
                                ),
                            ]
                        )
                    ],
                    readOnly: true,
                    required: true
                ),
                '@id' => new ApiProperty(nullable: false, readOnly: true, required: true),
                '@type' => new ApiProperty(nullable: false, readOnly: true, required: true)
            ]
        )]);
    }

    /**
     * @return array{bodies: ArrayObject<string, Model\RequestBody>, paths: Model\Paths, responses: ArrayObject<string, Model\Response>, schemas: ArrayObject<string, SchemaContext>}
     */
    #[ArrayShape([
        'bodies' => ArrayObject::class,
        'paths' => Model\Paths::class,
        'responses' => ArrayObject::class,
        'schemas' => ArrayObject::class
    ])]
    private function generateOpenApi(): array {
        /** @var Collection<string, Model\RequestBody> $bodies */
        $bodies = new Collection();
        $paths = new Model\Paths();
        /** @var Collection<string, Schema> $reads */
        $reads = new Collection();
        /** @var Collection<string, Model\Response> $responses */
        $responses = new Collection();
        $schemas = $this->createSchemas();

        foreach ($this->classCollector->getClasses() as $refl) {
            $this->generateSchemas($reads, $refl, $schemas);
            $this->generatePaths($bodies, $paths, $refl, $responses);
        }

        foreach ($reads as $read) {
            foreach ($read->getParents() as $parent) {
                if (null !== $schema = $schemas->get($parent)) {
                    $read->appendRequired($schema->getNotRequired());
                }
            }
        }

        /** @var array<string, SchemaContext> $schemas */
        $schemas = $schemas->map->getOpenApiContext()->all();
        return [
            'bodies' => new ArrayObject($bodies->all()),
            'paths' => $paths,
            'responses' => new ArrayObject($responses->all()),
            'schemas' => new ArrayObject($schemas)
        ];
    }

    /**
     * @param Operation $operation
     */
    private function generateOperation(ResourceMetadata $metadata, array $operation, Model\PathItem $path): Model\PathItem {
        if ($operation['openapi_context']['summary'] === 'hidden') {
            return $path;
        }
        if (
            in_array($operation['method'], ['DELETE', 'GET'])
            || empty($denormalizationContext = $metadata->getAttribute('denormalization_context', []))
        ) {
            $body = null;
        } else {
            $bodyRef = "#/components/requestBodies/{$denormalizationContext['openapi_definition_name']}";
            if ($operation['method'] === 'PATCH') {
                $bodyRef .= '-patch';
            }
            $body = ['$ref' => $bodyRef];
        }
        return $path->{'with'.ucfirst(strtolower($operation['method']))}(new Model\Operation(
            tags: [$metadata->getShortName()],
            responses: [
                match ($operation['method']) {
                    'DELETE' => 204,
                    'POST' => 201,
                    default => 200
                } => empty($normalizationContext = $metadata->getAttribute('normalization_context', []))
                    ? ['description' => 'Succès']
                    : ['$ref' => "#/components/responses/{$normalizationContext['openapi_definition_name']}"]
            ],
            summary: $operation['openapi_context']['summary'],
            description: $operation['openapi_context']['description'],
            requestBody: $body
        ));
    }

    /**
     * @param null|Operation[]   $operations
     * @param ReflectionClass<T> $refl
     *
     * @template T of object
     */
    private function generateOperations(ResourceMetadata $metadata, ?array $operations, Model\Paths $paths, ReflectionClass $refl, string $type): void {
        if (!empty($operations)) {
            foreach ($operations as $operation) {
                $path = $this->getPath($metadata->getShortName() ?? $refl->getShortName(), $operation, $type);
                $paths->addPath(
                    $path,
                    $this->generateOperation(
                        metadata: $metadata,
                        operation: $operation,
                        path: $paths->getPath($path) ?? new Model\PathItem(
                            parameters: $type === OperationType::ITEM ? [['$ref' => '#/components/parameters/id']] : []
                        )
                    )
                );
            }
        }
    }

    /**
     * @param Collection<string, Model\RequestBody> $bodies
     * @param ReflectionClass<T>                    $refl
     * @param Collection<string, Model\Response>    $responses
     *
     * @template T of object
     */
    private function generatePaths(Collection $bodies, Model\Paths $paths, ReflectionClass $refl, Collection $responses): void {
        try {
            $metadata = $this->metadataFactory->create($refl->getName());
            if (!empty($denormalizationContext = $metadata->getAttribute('denormalization_context', []))) {
                $bodies[$denormalizationContext['openapi_definition_name']] = new Model\RequestBody(
                    description: self::getReflDescription($refl) ?? $metadata->getShortName() ?? $refl->getShortName(),
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => "#/components/schemas/{$denormalizationContext['openapi_definition_name']}"
                            ]
                        ]
                    ]),
                    required: true
                );
                $bodies["{$denormalizationContext['openapi_definition_name']}-patch"] = new Model\RequestBody(
                    description: self::getReflDescription($refl) ?? $metadata->getShortName() ?? $refl->getShortName(),
                    content: new ArrayObject([
                        'application/merge-patch+json' => [
                            'schema' => [
                                '$ref' => "#/components/schemas/{$denormalizationContext['openapi_definition_name']}"
                            ]
                        ]
                    ]),
                    required: true
                );
            }
            if (!empty($normalizationContext = $metadata->getAttribute('normalization_context', []))) {
                $responses[$normalizationContext['openapi_definition_name']] = new Model\Response(
                    description: self::getReflDescription($refl) ?? $metadata->getShortName() ?? $refl->getShortName(),
                    content: new ArrayObject([
                        'application/ld+json' => [
                            'schema' => [
                                '$ref' => "#/components/schemas/{$normalizationContext['openapi_definition_name']}"
                            ]
                        ]
                    ])
                );
            }
            $this->generateOperations($metadata, $metadata->getCollectionOperations(), $paths, $refl, OperationType::COLLECTION);
            $this->generateOperations($metadata, $metadata->getItemOperations(), $paths, $refl, OperationType::ITEM);
        } catch (ResourceClassNotFoundException) {
        }
    }

    /**
     * @param Collection<string, array{apiProperty: ApiProperty, groups: Serializer\Groups}> $properties
     * @param ReflectionClass<T>                                                             $refl
     * @param string[]                                                                       $parents
     *
     * @template T of object
     */
    private function generateSchema(string $group, Collection $properties, ReflectionClass $refl, array $parents = []): ?Schema {
        $parents = collect($parents);
        $properties = $properties
            ->mapWithKeys(static fn (array $attributes, string $property): array => $attributes['apiProperty']->hasRef()
            || (in_array($group, $attributes['groups']->getGroups())
                && $parents->intersect($attributes['groups']->getGroups())->isEmpty())
                ? [$property => $attributes['apiProperty']]
                : [])
            ->all();
        return empty($properties) ? null : new Schema(
            description: $parents->isEmpty() ? self::getReflDescription($refl) : null,
            properties: $properties
        );
    }

    /**
     * @param Collection<string, Schema> $reads
     * @param ReflectionClass<T>         $refl
     * @param Collection<string, Schema> $schemas
     *
     * @template T of object
     */
    private function generateSchemas(Collection $reads, ReflectionClass $refl, Collection $schemas): void {
        /** @var Collection<string, array{apiProperty: ApiProperty, groups: Serializer\Groups}> $properties */
        $properties = collect($refl->getProperties())
            ->mapWithKeys(static function (ReflectionProperty $property): array {
                $apiPropertyAttributes = $property->getAttributes(ApiProperty::class);
                $groupsAttributes = $property->getAttributes(Serializer\Groups::class);
                if (count($apiPropertyAttributes) === 1 && count($groupsAttributes) === 1) {
                    /** @var ApiProperty $apiProperty */
                    $apiProperty = $apiPropertyAttributes[0]->newInstance();
                    return [$property->getName() => [
                        'apiProperty' => $apiProperty->setReflectionProperty($property),
                        'groups' => $groupsAttributes[0]->newInstance()
                    ]];
                }
                return [];
            });
        $attributes = $refl->getAttributes(ApiSerializerGroups::class);
        if (count($attributes) === 1) {
            /** @var ApiSerializerGroups $groups */
            $groups = $attributes[0]->newInstance();
            foreach ($groups->inheritedRead as $group => $parents) {
                $allOf = (new ArrayObject($parents))->getArrayCopy();
                if (!empty($schema = $this->generateSchema($group, $properties, $refl, $parents))) {
                    $allOf[] = $schema;
                }
                $schema = new Schema(allOf: $allOf, description: self::getReflDescription($refl));
                $schemas->put($group, $schema);
                $reads->put($group, $schema);
            }
            /** @var Collection<string, array{apiProperty: ApiProperty, groups: Serializer\Groups}> $writeProperties */
            $writeProperties = $properties->map(static fn (array $attributes): array => [
                'apiProperty' => (clone $attributes['apiProperty'])->setReadMode(false),
                'groups' => $attributes['groups']
            ]);
            foreach ($groups->write as $group) {
                if (!empty($schema = $this->generateSchema(
                    group: $group,
                    properties: $writeProperties,
                    refl: $refl
                ))) {
                    $schemas->put($group, $schema);
                    $reads->put($group, $schema);
                }
            }
        }
    }

    #[Pure]
    private function getInfo(): Model\Info {
        return new Model\Info(
            title: $this->options->getTitle(),
            version: $this->options->getVersion(),
            description: $this->options->getDescription()
        );
    }

    /**
     * @param Operation $operation
     */
    private function getPath(string $resourceShortName, array $operation, string $operationType): string {
        $path = removeEnd($this->resolver->resolveOperationPath($resourceShortName, $operation, $operationType), '.{_format}');
        $path = str_starts_with($path, '/') ? $path : "/$path";
        return str_starts_with($path, '/api') ? $path : "/api$path";
    }
}
