<?php

namespace App\ApiPlatform\Core\OpenApi\Factory;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Options;
use App\ApiPlatform\Core\Annotation\ApiProperty;
use App\ApiPlatform\Core\Annotation\ApiSerializerGroups;
use App\Service\EntitiesReflectionClassCollector;
use ArrayObject;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\Pure;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @phpstan-import-type SchemaContext from Schema
 */
final class OpenApiFactory implements OpenApiFactoryInterface {
    public function __construct(
        private readonly EntitiesReflectionClassCollector $classCollector,
        private readonly Options $options
    ) {
    }

    /**
     * @param array{base_url?: string} $context
     */
    public function __invoke(array $context = []): OpenApi {
        return new OpenApi(
            info: $this->getInfo(),
            servers: [],
            paths: new Model\Paths(),
            components: new Model\Components(schemas: $this->generateSchemas())
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
     * @return ArrayObject<string, SchemaContext>
     */
    private function createSchemas(): ArrayObject {
        return new ArrayObject(['Resource' => (new Schema(
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
        ))->getOpenApiContext()]);
    }

    /**
     * @param Collection<string, array{apiProperty: ApiProperty, groups: Serializer\Groups}> $properties
     * @param ReflectionClass<T>                                                             $refl
     *
     * @template T of object
     */
    private function generateSchema(string $group, Collection $properties, ReflectionClass $refl, ?string $parent = null): ?Schema {
        $properties = $properties
            ->mapWithKeys(static fn (array $attributes, string $property): array => in_array($group, $attributes['groups']->getGroups())
            && (empty($parent) || !in_array($parent, $attributes['groups']->getGroups()))
                ? [$property => $attributes['apiProperty']]
                : [])
            ->all();
        return empty($properties) ? null : new Schema(description: self::getReflDescription($refl), properties: $properties);
    }

    /**
     * @return ArrayObject<string, SchemaContext>
     */
    private function generateSchemas(): ArrayObject {
        $schemas = $this->createSchemas();
        foreach ($this->classCollector->getClasses() as $refl) {
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
                foreach ($groups->inheritedRead as $base => $children) {
                    foreach ($children as $group) {
                        $allOf = [$base];
                        if (!empty($schema = $this->generateSchema($group, $properties, $refl, $base))) {
                            $allOf[] = $schema;
                        }
                        $schemas[$group] = (new Schema(allOf: $allOf))->getOpenApiContext();
                    }
                }
                foreach ($groups->write as $group) {
                    if (!empty($schema = $this->generateSchema($group, $properties, $refl))) {
                        $schemas[$group] = $schema->getOpenApiContext();
                    }
                }
            }
        }
        return $schemas;
    }

    #[Pure]
    private function getInfo(): Model\Info {
        return new Model\Info(
            title: $this->options->getTitle(),
            version: $this->options->getVersion(),
            description: $this->options->getDescription()
        );
    }
}
