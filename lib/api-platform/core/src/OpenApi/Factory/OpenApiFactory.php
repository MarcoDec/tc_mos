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
     * @return ArrayObject<string, SchemaContext>
     */
    private function generateSchemas(): ArrayObject {
        $schemas = $this->createSchemas();
        /** @var Collection<string, Schema> $reads */
        $reads = new Collection();
        foreach ($this->classCollector->getClasses() as $refl) {
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
        foreach ($reads as $read) {
            foreach ($read->getParents() as $parent) {
                if (null !== $schema = $schemas->get($parent)) {
                    $read->appendRequired($schema->getNotRequired());
                }
            }
        }
        /** @var array<string, SchemaContext> $schemas */
        $schemas = $schemas->map->getOpenApiContext()->all();
        return new ArrayObject($schemas);
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
