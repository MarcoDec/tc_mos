<?php

namespace App\ApiPlatform\Core\OpenApi\Factory;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Options;
use App\ApiPlatform\Core\Annotation\ApiProperty;
use ArrayObject;
use JetBrains\PhpStorm\Pure;

final class OpenApiFactory implements OpenApiFactoryInterface {
    public function __construct(private readonly Options $options) {
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
     * @return ArrayObject<string, Schema>
     */
    private function generateSchemas(): ArrayObject {
        return new ArrayObject(['Resource' => new Schema(
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
        )]);
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
