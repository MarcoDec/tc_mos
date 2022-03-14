<?php

namespace App\ApiPlatform\Core\OpenApi\Factory;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Options;
use App\ApiPlatform\Core\Annotation\ApiProperty;
use App\ApiPlatform\Core\Annotation\ApiSerializerGroups;
use ArrayObject;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;

/**
 * @phpstan-import-type SchemaContext from Schema
 */
final class OpenApiFactory implements OpenApiFactoryInterface {
    public function __construct(private readonly EntityManagerInterface $em, private readonly Options $options) {
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
     * @return ArrayObject<string, SchemaContext>
     */
    private function createSchemas(): ArrayObject {
        return new ArrayObject(['Resource' => (new Schema(
            description: 'Base d\'une resource',
            properties: [
                '@context' => new ApiProperty(
                    oneOf: [
                        new ApiProperty(),
                        new Schema(
                            additionalProperties: true,
                            properties: [
                                '@vocab' => new ApiProperty(readOnly: true, required: true),
                                'hydra' => new ApiProperty(
                                    enum: ['http://www.w3.org/ns/hydra/core#'],
                                    readOnly: true,
                                    required: true
                                ),
                            ]
                        )
                    ],
                    readOnly: true,
                    required: true
                ),
                '@id' => new ApiProperty(readOnly: true, required: true),
                '@type' => new ApiProperty(readOnly: true, required: true)
            ]
        ))->getOpenApiContext()]);
    }

    /**
     * @return ArrayObject<string, SchemaContext>
     */
    private function generateSchemas(): ArrayObject {
        $schemas = $this->createSchemas();
        foreach ($this->em->getMetadataFactory()->getAllMetadata() as $metadata) {
            $attributes = $metadata->getReflectionClass()->getAttributes(ApiSerializerGroups::class);
            if (count($attributes) === 1) {
                /** @var ApiSerializerGroups $groups */
                $groups = $attributes[0]->newInstance();
                foreach ($groups->inheritedRead as $base => $children) {
                    foreach ($children as $group) {
                        $schemas[$group] = (new Schema(allOf: [
                            $base,
                            new Schema()
                        ]))->getOpenApiContext();
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
