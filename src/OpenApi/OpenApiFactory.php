<?php

namespace App\OpenApi;

use ApiPlatform\Core\Exception\ResourceClassNotFoundException;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Components;
use ApiPlatform\Core\OpenApi\Model\Info;
use ApiPlatform\Core\OpenApi\Model\Paths;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Options;
use ArrayObject;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Mapping\ClassMetadata;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation as Serializer;

final class OpenApiFactory implements OpenApiFactoryInterface {
    public function __construct(
        private EntityManagerInterface $em,
        private Options $options,
        private ResourceMetadataFactoryInterface $metadataFactory
    ) {
    }

    /**
     * @param array{base_url?: string} $context
     */
    public function __invoke(array $context = []): OpenApi {
        return new OpenApi(
            info: $this->getInfo(),
            servers: [],
            paths: new Paths(),
            components: new Components(schemas: $this->generateSchema())
        );
    }

    /**
     * @return ArrayObject<string, Schema>
     */
    private function createSchemas(): ArrayObject {
        return new ArrayObject([
            'Resource' => new Schema(
                description: 'Base d\'une resource',
                properties: [
                    '@context' => [
                        'oneOf' => [
                            ['type' => 'string'],
                            [
                                'additionalProperties' => true,
                                'properties' => [
                                    '@vocab' => ['type' => 'string'],
                                    'hydra' => [
                                        'enum' => ['http://www.w3.org/ns/hydra/core#'],
                                        'type' => 'string'
                                    ]
                                ],
                                'required' => ['@vocab', 'hydra'],
                                'type' => 'object'
                            ]
                        ],
                        'readOnly' => true
                    ],
                    '@id' => ['readOnly' => true, 'type' => 'string'],
                    '@type' => ['readOnly' => true, 'type' => 'string']
                ],
                required: ['@context', '@id', '@type'],
                type: 'object'
            )
        ]);
    }

    /**
     * @return ArrayObject<string, Schema>
     */
    private function generateSchema(): ArrayObject {
        $schemas = $this->createSchemas();
        foreach ($this->getOrmMetadatas() as $ormMetadata) {
            try {
                $metadata = $this->metadataFactory->create($ormMetadata->getName());
                $groups = [];
                foreach ($ormMetadata->getReflectionClass()->getProperties() as $property) {
                    if ($property->getDeclaringClass()->getName() === $ormMetadata->getReflectionClass()->getName()) {
                        foreach ($property->getAttributes(Serializer\Groups::class) as $attribute) {
                            /** @var Serializer\Groups $serializerGroups */
                            $serializerGroups = $attribute->newInstance();
                            foreach ($serializerGroups->getGroups() as $group) {
                                if (!isset($groups[$group])) {
                                    $groups[$group] = [];
                                }
                                $groups[$group][] = $property->getName();
                                sort($groups[$group]);
                            }
                        }
                    }
                }
                if (count($groups) === 1) {
                    $schemas[$metadata->getShortName()] = new Schema();
                } else {
                    ksort($groups);
                    foreach ($groups as $group => $properties) {
                        $schemas[$group] = new Schema();
                    }
                }
            } catch (ResourceClassNotFoundException $e) {
            }
        }
        return $schemas;
    }

    #[Pure]
    private function getInfo(): Info {
        return new Info(
            title: $this->options->getTitle(),
            version: $this->options->getVersion(),
            description: $this->options->getDescription()
        );
    }

    /**
     * @return ClassMetadata<object>[]
     */
    private function getOrmMetadatas(): array {
        return $this->em->getMetadataFactory()->getAllMetadata();
    }
}
