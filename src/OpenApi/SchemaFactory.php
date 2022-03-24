<?php

namespace App\OpenApi;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Exception\ResourceClassNotFoundException;
use ApiPlatform\Core\JsonSchema\Schema;
use ApiPlatform\Core\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Operation\DashPathSegmentNameGenerator;
use App\Entity\Embeddable\Measure;
use ArrayObject;
use Illuminate\Support\Collection;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;

final class SchemaFactory implements SchemaFactoryInterface {
    public function __construct(
        private readonly DashPathSegmentNameGenerator $dashGenerator,
        private readonly SchemaFactoryInterface $decorated,
        private readonly ResourceMetadataFactoryInterface $resourceMetadataFactory
    ) {
    }

    /**
     * @param class-string $className
     */
    private static function isRequired(string $className, string $propertyName): bool {
        $refl = new ReflectionClass($className);
        /** @var Collection<int, ReflectionAttribute<ApiProperty>> $attrs */
        $attrs = new Collection();
        try {
            $attrs = $attrs->merge($refl->getProperty($propertyName)->getAttributes(ApiProperty::class));
        } catch (ReflectionException) {
        }
        try {
            $attrs = $attrs->merge($refl->getMethod('get'.ucfirst($propertyName))->getAttributes(ApiProperty::class));
        } catch (ReflectionException) {
        }
        foreach ($attrs as $attr) {
            /** @var ApiProperty $apiProperty */
            $apiProperty = $attr->newInstance();
            if ($apiProperty->required) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array<string, mixed>|ArrayObject<string, mixed> $array
     */
    private static function ksort(array|ArrayObject &$array): void {
        if ($array instanceof ArrayObject) {
            $array->ksort();
        } else {
            ksort($array);
        }
    }

    /**
     * @param class-string               $className
     * @param null|Schema<string, mixed> $schema
     * @param mixed[]                    $serializerContext
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

        if (!empty($key) && in_array($format, ['json', 'jsonld'])) {
            foreach (array_keys($definitions[$key]['properties']) as $property) {
                if (isset($definitions[$key]['properties'][$property]['description'])) {
                    $definitions[$key]['properties'][$property]['title'] = $definitions[$key]['properties'][$property]['description'];
                }
                self::ksort($definitions[$key]['properties'][$property]);
            }
            self::ksort($definitions[$key]['properties']);
        }

        if ('jsonld' !== $format) {
            return $schema;
        }

        if (!empty($key)) {
            try {
                $resourceName = $this->resourceMetadataFactory->create($className)->getShortName();
            } catch (ResourceClassNotFoundException) {
                return $schema;
            }
            if (!empty($resourceName)) {
                $definitions[$key]['properties']['@context']['example'] = "/api/contexts/$resourceName";
                $definitions[$key]['properties']['@id']['example'] = "/api/{$this->dashGenerator->getSegmentName($resourceName)}/1";
                $definitions[$key]['properties']['@type']['example'] = $resourceName;
                if ($type === Schema::TYPE_OUTPUT) {
                    if (!isset($definitions[$key]['required'])) {
                        $definitions[$key]['required'] = [];
                    }
                    $jsonldRequired = ['@context', '@id', '@type'];
                    foreach ($jsonldRequired as $property) {
                        if (!in_array($property, $definitions[$key]['required'])) {
                            $definitions[$key]['required'][] = $property;
                        }
                    }
                    if (isset($definitions[$key]['properties']['id']) && !in_array('id', $definitions[$key]['required'])) {
                        $definitions[$key]['required'][] = 'id';
                    }
                }
                foreach (array_keys($definitions[$key]['properties']) as $property) {
                    /** @var string $property */
                    if (self::isRequired($className, $property)) {
                        if (!isset($definitions[$key]['required'])) {
                            $definitions[$key]['required'] = [];
                        }
                        if (!in_array($property, $definitions[$key]['required'])) {
                            $definitions[$key]['required'][] = $property;
                        }
                        $definitions[$key]['properties'][$property]['nullable'] = false;
                    }
                    self::ksort($definitions[$key]['properties'][$property]);
                }
                self::ksort($definitions[$key]['properties']);
            }
        }

        return $schema;
    }
}
