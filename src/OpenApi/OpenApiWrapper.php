<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\Paths;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\Model\Response;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\Operation\DashPathSegmentNameGenerator;
use ArrayObject;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Intl\Countries;

final class OpenApiWrapper {
    public function __construct(private OpenApi $api, private DashPathSegmentNameGenerator $dashGenerator) {
    }

    #[Pure]
    private static function securizeOperation(Operation $operation, string $schema): Operation {
        return $operation->withSecurity([[$schema => []]]);
    }

    private static function setDefaultOperationResponses(Operation $operation): Operation {
        $responses = collect($operation->getResponses());
        if (!$responses->offsetExists(400)) {
            $operation
                ->addResponse(
                    response: new Response(description: 'Bad request'),
                    status: 400
                );
        }
        if (!$responses->offsetExists(401)) {
            $operation
                ->addResponse(
                    response: new Response(description: 'Unauthorized'),
                    status: 401
                );
        }
        if (!$responses->offsetExists(403)) {
            $operation
                ->addResponse(
                    response: new Response(description: 'Forbidden'),
                    status: 403
                );
        }
        if (!$responses->offsetExists(405)) {
            $operation
                ->addResponse(
                    response: new Response(description: 'Method Not Allowed'),
                    status: 405
                );
        }
        if (!$responses->offsetExists(500)) {
            $operation
                ->addResponse(
                    response: new Response(description: 'Internal Server Error'),
                    status: 500
                );
        }
        return $operation->withResponses(
            collect($operation->getResponses())
                ->filter(static fn (Response $response): bool => $response->getDescription() !== 'hidden')
                ->all()
        );
    }

    /**
     * @param mixed[]                                    $responses
     * @param array{description: string, schema: string} $requestBody
     */
    public function addPath(string $id, string $path, string $tag, array $responses, string $description, ?array $requestBody = null): self {
        $this->api->getPaths()->addPath($path, new PathItem(
            post: new Operation(
                operationId: $id,
                tags: [$tag],
                responses: collect($responses)
                    ->map(static fn (array $response): Response => new Response(
                        description: $response['description'],
                        content: isset($response['schema']) ? new ArrayObject([
                            'application/ld+json' => [
                                'schema' => [
                                    '$ref' => "#/components/schemas/{$response['schema']['tag']}.jsonld-{$response['schema']['value']}"
                                ]
                            ]
                        ]) : null
                    ))
                    ->all(),
                summary: $description,
                description: $description,
                requestBody: !empty($requestBody) ? new RequestBody(
                    description: $requestBody['description'],
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => "#/components/schemas/{$requestBody['schema']}"
                            ]
                        ]
                    ])
                ) : null
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

    public function securize(string $login, string $schema): self {
        $paths = new Paths();
        foreach ($this->api->getPaths()->getPaths() as $path => $item) {
            /** @var PathItem $item */
            if ($path !== $login) {
                if (!empty($delete = $item->getDelete())) {
                    $item = $item->withDelete(self::securizeOperation($delete, $schema));
                }
                if (!empty($get = $item->getGet())) {
                    $item = $item->withGet(self::securizeOperation($get, $schema));
                }
                if (!empty($head = $item->getHead())) {
                    $item = $item->withHead(self::securizeOperation($head, $schema));
                }
                if (!empty($option = $item->getOptions())) {
                    $item = $item->withOptions(self::securizeOperation($option, $schema));
                }
                if (!empty($patch = $item->getPatch())) {
                    $item = $item->withPatch(self::securizeOperation($patch, $schema));
                }
                if (!empty($post = $item->getPost())) {
                    $item = $item->withPost(self::securizeOperation($post, $schema));
                }
                if (!empty($put = $item->getPut())) {
                    $item = $item->withPut(self::securizeOperation($put, $schema));
                }
                if (!empty($trace = $item->getTrace())) {
                    $item = $item->withTrace(self::securizeOperation($trace, $schema));
                }
            }
            $paths->addPath($path, $item);
        }
        $this->api = $this->api->withPaths($paths);
        return $this;
    }

    public function setDefaultResponses(): self {
        $paths = new Paths();
        foreach ($this->api->getPaths()->getPaths() as $path => $item) {
            if (!empty($delete = $item->getDelete())) {
                $item = $item->withDelete(self::setDefaultOperationResponses($delete));
            }
            if (!empty($get = $item->getGet())) {
                $item = $item->withGet(self::setDefaultOperationResponses($get));
            }
            if (!empty($head = $item->getHead())) {
                $item = $item->withHead(self::setDefaultOperationResponses($head));
            }
            if (!empty($option = $item->getOptions())) {
                $item = $item->withOptions(self::setDefaultOperationResponses($option));
            }
            if (!empty($patch = $item->getPatch())) {
                $item = $item->withPatch(self::setDefaultOperationResponses($patch));
            }
            if (!empty($post = $item->getPost())) {
                $item = $item->withPost(self::setDefaultOperationResponses($post));
            }
            if (!empty($put = $item->getPut())) {
                $item = $item->withPut(self::setDefaultOperationResponses($put));
            }
            if (!empty($trace = $item->getTrace())) {
                $item = $item->withTrace(self::setDefaultOperationResponses($trace));
            }
            $paths->addPath($path, $item);
        }
        $this->api = $this->api->withPaths($paths);
        return $this;
    }

    public function setJsonLdDoc(): self {
        $apiSchemas = $this->api->getComponents()->getSchemas();
        if (empty($apiSchemas)) {
            return $this;
        }

        $schemas = collect();
        /** @var ArrayObject<string, mixed[]> $schema */
        foreach ($apiSchemas as $schemaName => $schema) {
            $resourceName = explode('.', $schemaName)[0];
            /** @var mixed[] $properties */
            $properties = $schema['properties'];
            if (isset($properties['@context'])) {
                $properties['@context']['example'] = "/api/contexts/$resourceName";
            }
            if (isset($properties['@id'])) {
                $properties['@id']['example'] = "/api/{$this->dashGenerator->getSegmentName($resourceName)}/1";
            }
            if (isset($properties['@type'])) {
                $properties['@type']['example'] = $resourceName;
            }
            foreach ($properties as &$property) {
                if (isset($property['countries']) && $property['countries']) {
                    $property['enum'] = Countries::getCountryCodes();
                }
            }
            $schema['properties'] = $properties;
            $schemas->put($schemaName, $schema);
        }
        $this->api = $this->api->withComponents(
            $this->api->getComponents()->withSchemas(new ArrayObject($schemas->all()))
        );
        return $this;
    }
}
