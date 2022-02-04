<?php

namespace App\OpenApi\Factory;

use ApiPlatform\Core\Api\ResourceClassResolverInterface;
use ApiPlatform\Core\JsonSchema\Schema;
use ApiPlatform\Core\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use ApiPlatform\Core\OpenApi\Factory\OpenApiFactory;
use ApiPlatform\Core\Swagger\Serializer\DocumentationNormalizer;
use App\Entity\Entity;
use ReflectionClass;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class SchemaFactory implements SchemaFactoryInterface {
    /** @var array<string, bool> */
    private array $distinctFormats = ['jsonld' => true];

    public function __construct(
        private SchemaFactoryInterface $decorated,
        private ResourceClassResolverInterface $resourceClassResolver,
        private ResourceMetadataFactoryInterface $resourceMetadataFactory
    ) {
    }

    private static function encodeDefinitionName(string $name): string {
        return preg_replace('/[^a-zA-Z0-9.\-_]/', '.', $name) ?? $name;
    }

    /**
     * @param class-string<Entity>       $className
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
        if (!isset($serializerContext['root'])) {
            $serializerContext['root'] = $className;
        }
        $built = $this->decorated->buildSchema(
            className: $className,
            format: $format,
            type: $type,
            operationType: $operationType,
            operationName: $operationName,
            schema: $schema,
            serializerContext: $serializerContext,
            forceCollection: $forceCollection
        );
        if ($className !== $serializerContext['root']) {
            return $built;
        }

        $schema = $schema ? clone $schema : new Schema();
        if (null === $metadata = $this->getMetadata($className, $type, $operationType, $operationName, $serializerContext)) {
            return $schema;
        }
        [$resourceMetadata, $serializerContext, $inputOrOutputClass] = $metadata;
        $definitionName = $this->buildDefinitionName($className, $format, $inputOrOutputClass, $resourceMetadata, $serializerContext);
        if (!isset($built['components']['schemas'][$definitionName]['properties'])) {
            return $built;
        }

        $entitySchema = $built['components']['schemas'][$definitionName]['properties'];

        /* echo '<b>Definition Name</b><pre>';
        print_r($definitionName);
        echo '</pre>';
        echo '<b>Built</b><pre>';
        print_r($entitySchema);
        echo '</pre>'; */

        foreach ((new SchemaMetadata($className))->getMeasures() as $property => $measure) {
            if (isset($entitySchema[$property])) {
                /* echo '<b>'.$property.'</b><pre>';
                print_r($entitySchema[$property]);
                echo '</pre>'; */
            }
        }

        /* $schemaMetadata = new SchemaMetadata($className);
        echo '<b>Context</b><pre>';
        print_r($serializerContext);
        echo '</pre><b>Built</b><pre>';
        print_r(collect($built['components']['schemas'])->all());
        echo '</pre>'; */

        return $built;
    }

    /**
     * @param class-string<Entity> $className
     * @param mixed[]|null         $serializerContext
     */
    private function buildDefinitionName(
        string $className,
        string $format = 'json',
        ?string $inputOrOutputClass = null,
        ?ResourceMetadata $resourceMetadata = null,
        ?array $serializerContext = null
    ): string {
        $prefix = ($resourceMetadata ? $resourceMetadata->getShortName() : (new ReflectionClass($className))->getShortName()) ?? $className;
        if (null !== $inputOrOutputClass && $className !== $inputOrOutputClass) {
            $parts = explode('\\', $inputOrOutputClass);
            $shortName = end($parts);
            $prefix .= '.'.$shortName;
        }
        if (isset($this->distinctFormats[$format])) {
            // JSON is the default, and so isn't included in the definition name
            $prefix .= '.'.$format;
        }
        $definitionName = $serializerContext[OpenApiFactory::OPENAPI_DEFINITION_NAME] ?? $serializerContext[DocumentationNormalizer::SWAGGER_DEFINITION_NAME] ?? null;
        if ($definitionName) {
            $name = sprintf('%s-%s', $prefix, $definitionName);
        } else {
            $groups = (array) ($serializerContext[AbstractNormalizer::GROUPS] ?? []);
            $name = $groups ? sprintf('%s-%s', $prefix, implode('_', $groups)) : $prefix;
        }
        return self::encodeDefinitionName($name);
    }

    /**
     * @param mixed[]|null $serializerContext
     *
     * @return array{0: ResourceMetadata|null, 1: mixed[], 2: string}|null
     */
    private function getMetadata(
        string $className,
        string $type = Schema::TYPE_OUTPUT,
        ?string $operationType = null,
        ?string $operationName = null,
        ?array $serializerContext = null
    ): ?array {
        if (!$this->isResourceClass($className)) {
            return [null, $serializerContext ?? [], $className];
        }
        $resourceMetadata = $this->resourceMetadataFactory->create($className);
        $attribute = Schema::TYPE_OUTPUT === $type ? 'output' : 'input';
        $inputOrOutput = $operationType === null || $operationName === null
            ? $resourceMetadata->getAttribute($attribute, ['class' => $className])
            : $resourceMetadata->getTypedOperationAttribute($operationType, $operationName, $attribute, ['class' => $className], true);
        return isset($inputOrOutput['class']) && !empty($inputOrOutput['class'])
            ? [
                $resourceMetadata,
                $serializerContext ?? $this->getSerializerContext($className, $type, $operationType, $operationName),
                $inputOrOutput['class']
            ] : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function getSerializerContext(
        string $className,
        string $type = Schema::TYPE_OUTPUT,
        ?string $operationType = null,
        ?string $operationName = null
    ): array {
        $attribute = Schema::TYPE_OUTPUT === $type ? 'normalization_context' : 'denormalization_context';
        $resourceMetadata = $this->resourceMetadataFactory->create($className);
        return $operationType === null || $operationName === null
            ? $resourceMetadata->getAttribute($attribute, [])
            : $resourceMetadata->getTypedOperationAttribute($operationType, $operationName, $attribute, [], true);
    }

    private function isResourceClass(string $class): bool {
        return $this->resourceClassResolver->isResourceClass($class);
    }
}
