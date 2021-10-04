<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\Model\Response;
use ApiPlatform\Core\OpenApi\OpenApi;
use ArrayObject;

final class OpenApiWrapper {
    public function __construct(private OpenApi $api) {
    }

    /**
     * @param mixed[]                                    $responses
     * @param array{description: string, schema: string} $requestBody
     */
    public function addPath(string $id, string $path, string $tag, array $responses, string $description, array $requestBody): self {
        $this->api->getPaths()->addPath($path, new PathItem(
            post: new Operation(
                operationId: $id,
                tags: [$tag],
                responses: collect($responses)
                    ->map(static fn (array $response): Response => new Response(
                        description: $response['description'],
                        content: new ArrayObject([
                            'application/ld+json' => [
                                'schema' => [
                                    '$ref' => "#/components/schemas/{$response['schema']['tag']}.jsonld-{$response['schema']['value']}"
                                ]
                            ]
                        ])
                    ))
                    ->all(),
                summary: $description,
                description: $description,
                requestBody: new RequestBody(
                    description: $requestBody['description'],
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => "#/components/schemas/{$requestBody['schema']}"
                            ]
                        ]
                    ])
                )
            )
        ));
        return $this;
    }

    /**
     * @param array{properties: mixed[], type: string} $params
     */
    public function createSchema(string $name, array $params): self {
        $this->api->getComponents()->getSchemas()[$name] = new ArrayObject($params);
        return $this;
    }

    /**
     * @param string[] $params
     */
    public function createSecuritySchema(string $name, array $params): self {
        $this->api->getComponents()->getSecuritySchemes()[$name] = new ArrayObject($params);
        return $this;
    }

    public function getApi(): OpenApi {
        return $this->api;
    }

    public function hidePaths(): self {
        foreach ($this->api->getPaths()->getPaths() as $key => $path) {
            /** @var PathItem $path */
            if (!empty($path->getGet()) && $path->getGet()->getSummary() === 'hidden') {
                $this->api->getPaths()->addPath($key, $path->withGet(null));
            }
        }
        return $this;
    }
}
