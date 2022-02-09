<?php

namespace App\OpenApi;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\Paths;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\Model\Response;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\Operation\DashPathSegmentNameGenerator;
use ArrayObject;
use Error;
use Exception;
use JetBrains\PhpStorm\Pure;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

final class OpenApiWrapper {
    public function __construct(
        private OpenApi $api,
        private DashPathSegmentNameGenerator $dashGenerator,
        private OpenApiFactory $factory
    ) {
    }

    #[Pure]
    private static function securizeOperation(Operation $operation, string $schema): Operation {
        return $operation->withSecurity([[$schema => []]]);
    }

    private static function setDefaultOperationResponses(string $method, Operation $operation): Operation {
        $responses = collect($operation->getResponses());
        if (!$responses->offsetExists(HttpResponse::HTTP_BAD_REQUEST)) {
            $operation
                ->addResponse(
                    response: new Response('Bad request'),
                    status: HttpResponse::HTTP_BAD_REQUEST
                );
        }
        if (!$responses->offsetExists(HttpResponse::HTTP_UNAUTHORIZED)) {
            $operation
                ->addResponse(
                    response: new Response('Unauthorized'),
                    status: HttpResponse::HTTP_UNAUTHORIZED
                );
        }
        if (!$responses->offsetExists(HttpResponse::HTTP_FORBIDDEN)) {
            $operation
                ->addResponse(
                    response: new Response('Forbidden'),
                    status: HttpResponse::HTTP_FORBIDDEN
                );
        }
        if (!$responses->offsetExists(HttpResponse::HTTP_METHOD_NOT_ALLOWED)) {
            $operation
                ->addResponse(
                    response: new Response('Method Not Allowed'),
                    status: HttpResponse::HTTP_METHOD_NOT_ALLOWED
                );
        }
        if (in_array($method, ['patch', 'post'])) {
            $operation
                ->addResponse(
                    response: new Response(
                        description: 'Unprocessable entity',
                        content: new ArrayObject([
                            'application/ld+json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Violations'
                                ]
                            ]
                        ])
                    ),
                    status: HttpResponse::HTTP_UNPROCESSABLE_ENTITY
                );
        }
        if (!$responses->offsetExists(HttpResponse::HTTP_INTERNAL_SERVER_ERROR)) {
            $operation
                ->addResponse(
                    response: new Response('Internal Server Error'),
                    status: HttpResponse::HTTP_INTERNAL_SERVER_ERROR
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
     * @param array{properties: mixed[], required?: string[], type: string} $params
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
                $item = $item->withDelete(self::setDefaultOperationResponses('delete', $delete));
            }
            if (!empty($get = $item->getGet())) {
                $item = $item->withGet(self::setDefaultOperationResponses('get', $get));
            }
            if (!empty($head = $item->getHead())) {
                $item = $item->withHead(self::setDefaultOperationResponses('head', $head));
            }
            if (!empty($option = $item->getOptions())) {
                $item = $item->withOptions(self::setDefaultOperationResponses('option', $option));
            }
            if (!empty($patch = $item->getPatch())) {
                $item = $item->withPatch(self::setDefaultOperationResponses('patch', $patch));
            }
            if (!empty($post = $item->getPost())) {
                $item = $item->withPost(self::setDefaultOperationResponses('post', $post));
            }
            if (!empty($put = $item->getPut())) {
                $item = $item->withPut(self::setDefaultOperationResponses('put', $put));
            }
            if (!empty($trace = $item->getTrace())) {
                $item = $item->withTrace(self::setDefaultOperationResponses('trace', $trace));
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
            if (isset($schema['properties'])) {
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
                $schema['properties'] = $properties;

                try {
                    $refl = (new ReflectionClass($this->factory->getResourceClass($resourceName)));
                    $required = collect($schema['required'] ?? []);
                    // ->push('@context')
                    // ->push('@id')
                    // ->push('@type');
                    foreach (array_keys($schema['properties']) as $property) {
                        if ($refl->hasProperty($property)) {
                            foreach ($refl->getProperty($property)->getAttributes(ApiProperty::class) as $attribute) {
                                /** @var ApiProperty $instance */
                                $instance = $attribute->newInstance();
                                if ($instance->required) {
                                    $required->push($property);
                                }
                            }
                        }
                    }
                    if ($required->isEmpty() && isset($schema['required'])) {
                        unset($schema['required']);
                    } else {
                        $schema['required'] = $required->unique()->sort()->values()->all();
                    }
                } catch (Exception|Error $e) {
                }
            }
            $schemas->put($schemaName, $schema);
        }
        $this->api = $this->api->withComponents(
            $this->api->getComponents()->withSchemas(new ArrayObject($schemas->all()))
        );
        return $this;
    }
}
