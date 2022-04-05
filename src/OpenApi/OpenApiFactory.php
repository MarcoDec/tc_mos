<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\Api\IdentifiersExtractorInterface;
use ApiPlatform\Core\Api\OperationType;
use ApiPlatform\Core\DataProvider\PaginationOptions;
use ApiPlatform\Core\JsonSchema\Schema;
use ApiPlatform\Core\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Core\JsonSchema\TypeFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model;
use ApiPlatform\Core\OpenApi\Model\ExternalDocumentation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Options;
use ApiPlatform\Core\Operation\Factory\SubresourceOperationFactoryInterface;
use ApiPlatform\Core\PathResolver\OperationPathResolverInterface;
use const ARRAY_FILTER_USE_KEY;
use ArrayObject;
use function count;
use function in_array;
use function is_string;
use JetBrains\PhpStorm\Pure;
use Psr\Container\ContainerInterface;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use UnexpectedValueException;

final class OpenApiFactory implements OpenApiFactoryInterface {
    use FilterLocatorTrait;

    public const BASE_URL = 'base_url';

    /**
     * @param array<string, string[]> $formats
     */
    public function __construct(
        ContainerInterface $filterLocator,
        private array $formats,
        private IdentifiersExtractorInterface $identifiersExtractor,
        private SchemaFactoryInterface $jsonSchemaFactory,
        private TypeFactoryInterface $jsonSchemaTypeFactory,
        private Options $openApiOptions,
        private OperationPathResolverInterface $operationPathResolver,
        private PaginationOptions $paginationOptions,
        private ResourceMetadataFactoryInterface $resourceMetadataFactory,
        private ResourceNameCollectionFactoryInterface $resourceNameCollectionFactory,
        private SubresourceOperationFactoryInterface $subresourceOperationFactory,
        private UrlGeneratorInterface $urlGenerator
    ) {
        $this->setFilterLocator($filterLocator, true);
    }

    /**
     * @param array{base_url?: string} $context
     */
    public function __invoke(array $context = []): OpenApi {
        $baseUrl = $context[self::BASE_URL] ?? '/';
        $contact = null === $this->openApiOptions->getContactUrl() || null === $this->openApiOptions->getContactEmail() ? null : new Model\Contact($this->openApiOptions->getContactName(), $this->openApiOptions->getContactUrl(), $this->openApiOptions->getContactEmail());
        $license = null === $this->openApiOptions->getLicenseName() ? null : new Model\License($this->openApiOptions->getLicenseName(), $this->openApiOptions->getLicenseUrl());
        $info = new Model\Info($this->openApiOptions->getTitle(), $this->openApiOptions->getVersion(), trim($this->openApiOptions->getDescription()), $this->openApiOptions->getTermsOfService(), $contact, $license);
        $servers = '/' === $baseUrl || '' === $baseUrl ? [new Model\Server('/')] : [new Model\Server($baseUrl)];
        $paths = new Model\Paths();
        $paths->addPath($this->getLogin(), new PathItem(
            post: new Model\Operation(
                operationId: 'login',
                tags: ['Auth'],
                responses: [
                    200 => new Model\Response(
                        description: 'Utilisateur connecté',
                        content: new ArrayObject([
                            'application/ld+json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Employee.jsonld-Employee-read'
                                ]
                            ]
                        ])
                    ),
                    400 => new Model\Response(description: 'Bad request'),
                    401 => new Model\Response(description: 'Unauthorized'),
                    405 => new Model\Response(description: 'Method Not Allowed'),
                    500 => new Model\Response(description: 'Internal Server Error')
                ],
                summary: 'Connexion',
                description: 'Connexion',
                requestBody: new Model\RequestBody(
                    description: 'Identifiants',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Auth'
                            ]
                        ]
                    ])
                )
            )
        ));
        $paths->addPath($this->getLogout(), new PathItem(
            post: new Model\Operation(
                operationId: 'logout',
                tags: ['Auth'],
                responses: [
                    204 => new Model\Response(description: 'Déconnexion réussie'),
                    400 => new Model\Response(description: 'Bad request'),
                    401 => new Model\Response(description: 'Unauthorized'),
                    403 => new Model\Response(description: 'Forbidden'),
                    405 => new Model\Response(description: 'Method Not Allowed'),
                    500 => new Model\Response(description: 'Internal Server Error')
                ],
                summary: 'Déconnexion',
                description: 'Déconnexion',
                security: [['bearerAuth' => []]]
            )
        ));
        /** @var ArrayObject<string, ArrayObject<string, mixed>> $schemas */
        $schemas = new ArrayObject([
            'Auth' => new ArrayObject([
                'description' => 'Authentification',
                'properties' => [
                    'password' => [
                        'description' => 'Mot de passe',
                        'example' => 'super',
                        'type' => 'string'
                    ],
                    'username' => [
                        'description' => 'Identifiant',
                        'example' => 'super',
                        'type' => 'string'
                    ]
                ],
                'required' => ['password', 'username'],
                'type' => 'object'
            ]),
            'Measure-duration' => new ArrayObject([
                'description' => 'Temps',
                'properties' => [
                    'code' => [
                        'default' => 's',
                        'description' => 'Code',
                        'example' => 's',
                        'type' => 'string'
                    ],
                    'value' => [
                        'description' => 'Valeur',
                        'example' => 1,
                        'type' => 'number'
                    ]
                ],
                'type' => 'object'
            ]),
            'Measure-mass' => new ArrayObject([
                'description' => 'Masse',
                'properties' => [
                    'code' => [
                        'default' => 'kg',
                        'description' => 'Code',
                        'example' => 'kg',
                        'type' => 'string'
                    ],
                    'value' => [
                        'description' => 'Valeur',
                        'example' => 1,
                        'type' => 'number'
                    ]
                ],
                'type' => 'object'
            ]),
            'Measure-price' => new ArrayObject([
                'description' => 'Prix',
                'properties' => [
                    'code' => [
                        'default' => '€',
                        'description' => 'Code',
                        'example' => '€',
                        'type' => 'string'
                    ],
                    'value' => [
                        'description' => 'Valeur',
                        'example' => 1,
                        'type' => 'number'
                    ]
                ],
                'type' => 'object'
            ]),
            'Measure-unitary' => new ArrayObject([
                'description' => 'Unitaire',
                'properties' => [
                    'code' => [
                        'default' => 'U',
                        'description' => 'Code',
                        'example' => 'U',
                        'type' => 'string'
                    ],
                    'value' => [
                        'description' => 'Valeur',
                        'example' => 1,
                        'type' => 'number'
                    ]
                ],
                'type' => 'object'
            ]),
            'Violation' => new ArrayObject([
                'properties' => [
                    'code' => [
                        'example' => 'c1051bb4-d103-4f74-8988-acbcafc7fdc3',
                        'type' => 'string'
                    ],
                    'message' => [
                        'example' => 'This value should not be blank.',
                        'type' => 'string'
                    ],
                    'propertyPath' => [
                        'example' => 'name',
                        'type' => 'string'
                    ]
                ],
                'required' => ['code', 'message', 'propertyPath'],
                'type' => 'object'
            ]),
            'Violations' => new ArrayObject([
                'properties' => [
                    '@context' => [
                        'example' => '/api/contexts/ConstraintViolationList',
                        'type' => 'string'
                    ],
                    '@type' => [
                        'example' => 'ConstraintViolationList',
                        'type' => 'string'
                    ],
                    'hydra:title' => [
                        'example' => 'An error occurred',
                        'type' => 'string'
                    ],
                    'hydra:description' => [
                        'example' => "name: This value should not be blank.\nname: This value should not be blank.",
                        'type' => 'string'
                    ],
                    'violations' => [
                        'items' => [
                            '$ref' => '#/components/schemas/Violation'
                        ],
                        'type' => 'array'
                    ]
                ],
                'required' => ['violations'],
                'type' => 'object'
            ])
        ]);

        foreach ($this->resourceNameCollectionFactory->create() as $resourceClass) {
            $resourceMetadata = $this->resourceMetadataFactory->create($resourceClass);

            // Items needs to be parsed first to be able to reference the lines from the collection operation
            $this->collectPaths($resourceMetadata, $resourceClass, OperationType::ITEM, $paths, $schemas);
            $this->collectPaths($resourceMetadata, $resourceClass, OperationType::COLLECTION, $paths, $schemas);

            $this->collectPaths($resourceMetadata, $resourceClass, OperationType::SUBRESOURCE, $paths, $schemas);
        }

        $securitySchemes = $this->getSecuritySchemes();

        return new OpenApi(
            info: $info,
            servers: $servers,
            paths: $paths,
            components: new Model\Components(
                schemas: $schemas,
                securitySchemes: new ArrayObject($securitySchemes)
            )
        );
    }

    /**
     * @param ArrayObject<string, ArrayObject<string, mixed>> $schemas
     * @param ArrayObject<string, ArrayObject<string, mixed>> $definitions
     */
    private function appendSchemaDefinitions(ArrayObject $schemas, ArrayObject $definitions): void {
        foreach ($definitions as $key => $value) {
            $schemas[$key] = $value;
        }
    }

    /**
     * @param array<string, string>                $responseMimeTypes
     * @param array<string, Schema<string, mixed>> $operationSchemas
     *
     * @return ArrayObject<string, Model\MediaType>
     */
    private function buildContent(array $responseMimeTypes, array $operationSchemas): ArrayObject {
        $content = new ArrayObject();

        foreach ($responseMimeTypes as $mimeType => $format) {
            $content[$mimeType] = new Model\MediaType(new ArrayObject($operationSchemas[$format]->getArrayCopy(false)));
        }

        return $content;
    }

    /**
     * @param ArrayObject<string, ArrayObject<string, mixed>> $schemas
     */
    private function collectPaths(ResourceMetadata $resourceMetadata, string $resourceClass, string $operationType, Model\Paths $paths, ArrayObject $schemas): void {
        $resourceShortName = $resourceMetadata->getShortName();
        if (empty($resourceShortName)) {
            throw new UnexpectedValueException("Resource shortName of \"$resourceClass\" not found");
        }

        $operations = OperationType::COLLECTION === $operationType ? $resourceMetadata->getCollectionOperations() : (OperationType::ITEM === $operationType ? $resourceMetadata->getItemOperations() : $this->subresourceOperationFactory->create($resourceClass));
        if (!$operations) {
            return;
        }

        $rootResourceClass = $resourceClass;
        foreach ($operations as $operationName => $operation) {
            if (isset($operation['openapi_context']['summary']) && $operation['openapi_context']['summary'] === 'hidden') {
                continue;
            }

            if (OperationType::COLLECTION === $operationType && !$resourceMetadata->getItemOperations()) {
                $identifiers = [];
            } else {
                $identifiers = (array) ($operation['identifiers'] ?? $resourceMetadata->getAttribute('identifiers', $this->identifiersExtractor->getIdentifiersFromResourceClass($resourceClass)));
            }
            if (count($identifiers) > 1 && $resourceMetadata->getAttribute('composite_identifier', true)) {
                $identifiers = ['id'];
            }

            $resourceClass = $operation['resource_class'] ?? $rootResourceClass;
            $path = $this->getPath($resourceShortName, $operation, $operationType);
            $method = $resourceMetadata->getTypedOperationAttribute($operationType, $operationName, 'method', 'GET');

            if (!in_array($method, PathItem::$methods, true)) {
                continue;
            }

            [$requestMimeTypes, $responseMimeTypes] = $this->getMimeTypes($operationName, $operationType, $resourceMetadata);
            $operationId = $operation['openapi_context']['operationId'] ?? lcfirst($operationName).ucfirst($resourceShortName).ucfirst($operationType);
            $linkedOperationId = 'get'.ucfirst($resourceShortName).ucfirst(OperationType::ITEM);
            $pathItem = $paths->getPath($path) ?: new Model\PathItem();
            $forceSchemaCollection = OperationType::SUBRESOURCE === $operationType ? ($operation['collection'] ?? false) : false;
            $schema = new Schema('openapi');
            $schema->setDefinitions($schemas);

            $operationOutputSchemas = [];
            foreach ($responseMimeTypes as $operationFormat) {
                $operationOutputSchema = $this->jsonSchemaFactory->buildSchema($resourceClass, $operationFormat, Schema::TYPE_OUTPUT, $operationType, $operationName, $schema, null, $forceSchemaCollection);
                $operationOutputSchemas[$operationFormat] = $operationOutputSchema;
                $this->appendSchemaDefinitions($schemas, $operationOutputSchema->getDefinitions());
            }

            $parameters = [];
            $responses = [
                400 => new Model\Response(description: 'Bad request'),
                401 => new Model\Response(description: 'Unauthorized'),
                403 => new Model\Response(description: 'Forbidden'),
                405 => new Model\Response(description: 'Method Not Allowed'),
                500 => new Model\Response(description: 'Internal Server Error')
            ];

            if ($operation['openapi_context']['parameters'] ?? false) {
                foreach ($operation['openapi_context']['parameters'] as $parameter) {
                    $parameters[] = new Model\Parameter($parameter['name'], $parameter['in'], $parameter['description'] ?? '', $parameter['required'] ?? false, $parameter['deprecated'] ?? false, $parameter['allowEmptyValue'] ?? false, $parameter['schema'] ?? [], $parameter['style'] ?? null, $parameter['explode'] ?? false, $parameter['allowReserved '] ?? false, $parameter['example'] ?? null, isset($parameter['examples']) ? new ArrayObject($parameter['examples']) : null, isset($parameter['content']) ? new ArrayObject($parameter['content']) : null);
                }
            }

            // Set up parameters
            if (OperationType::ITEM === $operationType) {
                foreach ($identifiers as $parameterName => $identifier) {
                    $parameterName = is_string($parameterName) ? $parameterName : $identifier;
                    $parameter = new Model\Parameter($parameterName, 'path', 'Resource identifier', true, false, false, ['type' => 'string']);
                    if ($this->hasParameter($parameter, $parameters)) {
                        continue;
                    }

                    $parameters[] = $parameter;
                }
            } elseif (OperationType::COLLECTION === $operationType && 'GET' === $method) {
                foreach (array_merge($this->getPaginationParameters($resourceMetadata, $operationName), $this->getFiltersParameters($resourceMetadata, $operationName, $resourceClass)) as $parameter) {
                    if ($this->hasParameter($parameter, $parameters)) {
                        continue;
                    }

                    $parameters[] = $parameter;
                }
            } elseif (OperationType::SUBRESOURCE === $operationType) {
                foreach ($operation['identifiers'] as $parameterName => [$class, $property]) {
                    $parameter = new Model\Parameter($parameterName, 'path', $this->resourceMetadataFactory->create($class)->getShortName().' identifier', true, false, false, ['type' => 'string']);
                    if ($this->hasParameter($parameter, $parameters)) {
                        continue;
                    }

                    $parameters[] = $parameter;
                }

                if ($operation['collection']) {
                    $subresourceMetadata = $this->resourceMetadataFactory->create($resourceClass);
                    foreach (array_merge($this->getPaginationParameters($resourceMetadata, $operationName), $this->getFiltersParameters($subresourceMetadata, $operationName, $resourceClass)) as $parameter) {
                        if ($this->hasParameter($parameter, $parameters)) {
                            continue;
                        }

                        $parameters[] = $parameter;
                    }
                }
            }

            // Create responses
            switch ($method) {
                case 'GET':
                    $successStatus = (string) $resourceMetadata->getTypedOperationAttribute($operationType, $operationName, 'status', '200');
                    $responseContent = $this->buildContent($responseMimeTypes, $operationOutputSchemas);
                    $responses[$successStatus] = new Model\Response(sprintf('%s %s', $resourceShortName, OperationType::COLLECTION === $operationType ? 'collection' : 'resource'), $responseContent);
                    break;
                case 'POST':
                    $responseContent = $this->buildContent($responseMimeTypes, $operationOutputSchemas);
                    $successStatus = (string) $resourceMetadata->getTypedOperationAttribute($operationType, $operationName, 'status', '201');
                    $responses[$successStatus] = new Model\Response(sprintf('%s resource created', $resourceShortName), $responseContent);
                    $responses['400'] = new Model\Response('Invalid input');
                    $responses['422'] = new Model\Response('Unprocessable entity');
                    break;
                case 'PATCH':
                case 'PUT':
                    $successStatus = (string) $resourceMetadata->getTypedOperationAttribute($operationType, $operationName, 'status', '200');
                    $responseContent = $this->buildContent($responseMimeTypes, $operationOutputSchemas);
                    $responses[$successStatus] = new Model\Response(sprintf('%s resource updated', $resourceShortName), $responseContent);
                    $responses['400'] = new Model\Response('Invalid input');
                    $responses['422'] = new Model\Response('Unprocessable entity');
                    break;
                case 'DELETE':
                    $successStatus = (string) $resourceMetadata->getTypedOperationAttribute($operationType, $operationName, 'status', '204');
                    $responses[$successStatus] = new Model\Response(sprintf('%s resource deleted', $resourceShortName));
                    break;
            }

            if (OperationType::ITEM === $operationType) {
                $responses['404'] = new Model\Response('Resource not found');
            }

            if (!$responses) {
                $responses['default'] = new Model\Response('Unexpected error');
            }

            if ($contextResponses = $operation['openapi_context']['responses'] ?? false) {
                foreach ($contextResponses as $statusCode => $contextResponse) {
                    $responses[$statusCode] = new Model\Response($contextResponse['description'] ?? '', isset($contextResponse['content']) ? new ArrayObject($contextResponse['content']) : null, isset($contextResponse['headers']) ? new ArrayObject($contextResponse['headers']) : null);
                }
            }

            $requestBody = null;
            if ($contextRequestBody = $operation['openapi_context']['requestBody'] ?? false) {
                $requestBody = new Model\RequestBody($contextRequestBody['description'] ?? '', new ArrayObject($contextRequestBody['content']), $contextRequestBody['required'] ?? false);
            } elseif ('PUT' === $method || 'POST' === $method || 'PATCH' === $method) {
                $operationInputSchemas = [];
                foreach ($requestMimeTypes as $operationFormat) {
                    $operationInputSchema = $this->jsonSchemaFactory->buildSchema($resourceClass, $operationFormat, Schema::TYPE_INPUT, $operationType, $operationName, $schema, null, $forceSchemaCollection);
                    $operationInputSchemas[$operationFormat] = $operationInputSchema;
                    $this->appendSchemaDefinitions($schemas, $operationInputSchema->getDefinitions());
                }

                $requestBody = new Model\RequestBody(sprintf('The %s %s resource', 'POST' === $method ? 'new' : 'updated', $resourceShortName), $this->buildContent($requestMimeTypes, $operationInputSchemas), true);
            }

            $modelOperation = new Model\Operation(
                $operationId,
                $operation['openapi_context']['tags'] ?? (OperationType::SUBRESOURCE === $operationType ? $operation['shortNames'] : [$resourceShortName]),
                $responses,
                $operation['openapi_context']['summary'] ?? $this->getPathDescription($resourceShortName, $method, $operationType),
                $operation['openapi_context']['description'] ?? $this->getPathDescription($resourceShortName, $method, $operationType),
                isset($operation['openapi_context']['externalDocs']) ? new ExternalDocumentation($operation['openapi_context']['externalDocs']['description'] ?? null, $operation['openapi_context']['externalDocs']['url']) : null,
                $parameters,
                $requestBody,
                isset($operation['openapi_context']['callbacks']) ? new ArrayObject($operation['openapi_context']['callbacks']) : null,
                $operation['openapi_context']['deprecated'] ?? (bool) $resourceMetadata->getTypedOperationAttribute($operationType, $operationName, 'deprecation_reason', false, true),
                $operation['openapi_context']['security'] ?? null,
                $operation['openapi_context']['servers'] ?? null,
                array_filter($operation['openapi_context'] ?? [], static fn ($item) => preg_match('/^x-.*$/i', $item), ARRAY_FILTER_USE_KEY)
            );

            $pathItem = $pathItem->{'with'.ucfirst($method)}($modelOperation->withSecurity([['bearerAuth' => []]]));

            $paths->addPath($path, $pathItem);
        }
    }

    /**
     * @param array<string, string[]> $responseFormats
     *
     * @return array<string, string>
     */
    private function flattenMimeTypes(array $responseFormats): array {
        $responseMimeTypes = [];
        foreach ($responseFormats as $responseFormat => $mimeTypes) {
            foreach ($mimeTypes as $mimeType) {
                $responseMimeTypes[$mimeType] = $responseFormat;
            }
        }

        return $responseMimeTypes;
    }

    /**
     * @return Model\Parameter[]
     */
    private function getFiltersParameters(ResourceMetadata $resourceMetadata, string $operationName, string $resourceClass): array {
        $parameters = [];
        $resourceFilters = $resourceMetadata->getCollectionOperationAttribute($operationName, 'filters', [], true);
        foreach ($resourceFilters as $filterId) {
            if (!$filter = $this->getFilter($filterId)) {
                continue;
            }

            foreach ($filter->getDescription($resourceClass) as $name => $data) {
                $schema = $data['schema'] ?? (in_array($data['type'], Type::$builtinTypes, true) ? $this->jsonSchemaTypeFactory->getType(new Type($data['type'], false, null, $data['is_collection'] ?? false)) : ['type' => 'string']);

                $parameters[] = new Model\Parameter(
                    $name,
                    'query',
                    $data['description'] ?? '',
                    $data['required'] ?? false,
                    $data['openapi']['deprecated'] ?? false,
                    $data['openapi']['allowEmptyValue'] ?? true,
                    $schema,
                    'array' === $schema['type'] && in_array(
                        $data['type'],
                        [Type::BUILTIN_TYPE_ARRAY, Type::BUILTIN_TYPE_OBJECT],
                        true
                    ) ? 'deepObject' : 'form',
                    $data['openapi']['explode'] ?? ('array' === $schema['type']),
                    $data['openapi']['allowReserved'] ?? false,
                    $data['openapi']['example'] ?? null,
                    isset($data['openapi']['examples']
                    ) ? new ArrayObject($data['openapi']['examples']) : null
                );
            }
        }

        return $parameters;
    }

    private function getLogin(): string {
        return $this->urlGenerator->generate('login');
    }

    private function getLogout(): string {
        return $this->urlGenerator->generate('logout');
    }

    /**
     * @return array<array<string, string>>
     */
    private function getMimeTypes(string $operationName, string $operationType, ResourceMetadata $resourceMetadata): array {
        $requestFormats = $resourceMetadata->getTypedOperationAttribute($operationType, $operationName, 'input_formats', $this->formats, true);
        $responseFormats = $resourceMetadata->getTypedOperationAttribute($operationType, $operationName, 'output_formats', $this->formats, true);

        $requestMimeTypes = $this->flattenMimeTypes($requestFormats);
        $responseMimeTypes = $this->flattenMimeTypes($responseFormats);

        return [$requestMimeTypes, $responseMimeTypes];
    }

    /**
     * @return Model\Parameter[]
     */
    private function getPaginationParameters(ResourceMetadata $resourceMetadata, string $operationName): array {
        if (!$this->paginationOptions->isPaginationEnabled()) {
            return [];
        }

        $parameters = [];

        if ($resourceMetadata->getCollectionOperationAttribute($operationName, 'pagination_enabled', true, true)) {
            $parameters[] = new Model\Parameter($this->paginationOptions->getPaginationPageParameterName(), 'query', 'The collection page number', false, false, true, ['type' => 'integer', 'default' => 1]);

            if ($resourceMetadata->getCollectionOperationAttribute($operationName, 'pagination_client_items_per_page', $this->paginationOptions->getClientItemsPerPage(), true)) {
                $schema = [
                    'type' => 'integer',
                    'default' => $resourceMetadata->getCollectionOperationAttribute($operationName, 'pagination_items_per_page', 30, true),
                    'minimum' => 0,
                ];

                if (null !== $maxItemsPerPage = $resourceMetadata->getCollectionOperationAttribute($operationName, 'pagination_maximum_items_per_page', null, true)) {
                    $schema['maximum'] = $maxItemsPerPage;
                }

                $parameters[] = new Model\Parameter($this->paginationOptions->getItemsPerPageParameterName(), 'query', 'The number of items per page', false, false, true, $schema);
            }
        }

        if ($resourceMetadata->getCollectionOperationAttribute($operationName, 'pagination_client_enabled', $this->paginationOptions->getPaginationClientEnabled(), true)) {
            $parameters[] = new Model\Parameter($this->paginationOptions->getPaginationClientEnabledParameterName(), 'query', 'Enable or disable pagination', false, false, true, ['type' => 'boolean']);
        }

        return $parameters;
    }

    /**
     * @param array<string, mixed> $operation
     */
    private function getPath(string $resourceShortName, array $operation, string $operationType): string {
        $path = removeEnd($this->operationPathResolver->resolveOperationPath($resourceShortName, $operation, $operationType), '.{_format}');
        $path = str_starts_with($path, '/') ? $path : "/$path";
        return str_starts_with($path, '/api') ? $path : "/api$path";
    }

    private function getPathDescription(string $resourceShortName, string $method, string $operationType): string {
        switch ($method) {
            case 'GET':
                $pathSummary = OperationType::COLLECTION === $operationType ? 'Retrieves the collection of %s resources.' : 'Retrieves a %s resource.';
                break;
            case 'POST':
                $pathSummary = 'Creates a %s resource.';
                break;
            case 'PATCH':
                $pathSummary = 'Updates the %s resource.';
                break;
            case 'PUT':
                $pathSummary = 'Replaces the %s resource.';
                break;
            case 'DELETE':
                $pathSummary = 'Removes the %s resource.';
                break;
            default:
                return $resourceShortName;
        }

        return sprintf($pathSummary, $resourceShortName);
    }

    /**
     * @return array<string, Model\SecurityScheme>
     */
    #[Pure]
    private function getSecuritySchemes(): array {
        $securitySchemes = [];
        foreach ($this->openApiOptions->getApiKeys() as $key => $apiKey) {
            $description = sprintf('Value for the %s %s parameter.', $apiKey['name'], $apiKey['type']);
            $securitySchemes[$key] = new Model\SecurityScheme('apiKey', $description, $apiKey['name'], $apiKey['type']);
        }
        $securitySchemes['bearerAuth'] = new Model\SecurityScheme(type: 'http', scheme: 'bearer');
        return $securitySchemes;
    }

    /**
     * @param Model\Parameter[] $parameters
     */
    #[Pure]
    private function hasParameter(Model\Parameter $parameter, array $parameters): bool {
        foreach ($parameters as $existingParameter) {
            if ($existingParameter->getName() === $parameter->getName() && $existingParameter->getIn() === $parameter->getIn()) {
                return true;
            }
        }

        return false;
    }
}
