<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\Api\IdentifiersExtractorInterface;
use ApiPlatform\Core\DataProvider\PaginationOptions;
use ApiPlatform\Core\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Core\JsonSchema\TypeFactoryInterface;
use ApiPlatform\Core\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Components;
use ApiPlatform\Core\OpenApi\Model\Contact;
use ApiPlatform\Core\OpenApi\Model\Info;
use ApiPlatform\Core\OpenApi\Model\License;
use ApiPlatform\Core\OpenApi\Model\OAuthFlow;
use ApiPlatform\Core\OpenApi\Model\OAuthFlows;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\Paths;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\Model\Response;
use ApiPlatform\Core\OpenApi\Model\SecurityScheme;
use ApiPlatform\Core\OpenApi\Model\Server;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Options;
use ApiPlatform\Core\PathResolver\OperationPathResolverInterface;
use App\OpenApi\Factory\CollectionPaths;
use App\OpenApi\Factory\ItemPaths;
use App\OpenApi\Factory\Links;
use ArrayObject;
use JetBrains\PhpStorm\Pure;
use LogicException;
use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class OpenApiFactory implements OpenApiFactoryInterface {
    public const BASE_URL = 'base_url';

    private Paths $paths;

    /** @var ArrayObject<string, mixed> */
    private ArrayObject $schemas;

    /**
     * @param array<string, string[]> $formats
     */
    public function __construct(
        private ContainerInterface $filterLocator,
        private array $formats,
        private IdentifiersExtractorInterface $identifiersExtractor,
        private SchemaFactoryInterface $jsonSchemaFactory,
        private TypeFactoryInterface $jsonSchemaTypeFactory,
        protected OperationPathResolverInterface $operationPathResolver,
        private PaginationOptions $paginationOptions,
        private PropertyMetadataFactoryInterface $propertyMetadataFactory,
        private PropertyNameCollectionFactoryInterface $propertyNameCollectionFactory,
        private ResourceMetadataFactoryInterface $resourceMetadataFactory,
        private ResourceNameCollectionFactoryInterface $resourceNameCollectionFactory,
        private Options $openApiOptions,
        private UrlGeneratorInterface $urlGenerator
    ) {
        $this->paths = new Paths();
        $this->schemas = new ArrayObject();
    }

    /**
     * @param array{base_url?: string} $context
     */
    public function __invoke(array $context = []): OpenApi {
        $baseUrl = $context[self::BASE_URL] ?? '/';
        $links = new Links();
        $servers = [new Server(empty($baseUrl) ? '/' : $baseUrl)];

        foreach ($this->resourceNameCollectionFactory->create() as $resourceClass) {
            $metadata = $this->resourceMetadataFactory->create($resourceClass);
            (new ItemPaths(
                formats: $this->formats,
                identifiersExtractor: $this->identifiersExtractor,
                jsonSchemaFactory: $this->jsonSchemaFactory,
                links: $links,
                operationPathResolver: $this->operationPathResolver,
                propertyMetadataFactory: $this->propertyMetadataFactory,
                propertyNameCollectionFactory: $this->propertyNameCollectionFactory,
                resourceClass: $resourceClass,
                resourceMetadata: $metadata
            ))->collect($this->paths, $this->schemas);
            (new CollectionPaths(
                filterLocator: $this->filterLocator,
                formats: $this->formats,
                jsonSchemaFactory: $this->jsonSchemaFactory,
                jsonSchemaTypeFactory: $this->jsonSchemaTypeFactory,
                links: $links,
                operationPathResolver: $this->operationPathResolver,
                paginationOptions: $this->paginationOptions,
                propertyMetadataFactory: $this->propertyMetadataFactory,
                propertyNameCollectionFactory: $this->propertyNameCollectionFactory,
                resourceClass: $resourceClass,
                resourceMetadata: $metadata
            ))->collect($this->paths, $this->schemas);
        }

        $securitySchemes = $this->getSecuritySchemes();
        $securityRequirements = [];
        foreach (array_keys($securitySchemes) as $key) {
            $securityRequirements[] = [$key => []];
        }
        $this->setSecurityPaths();

        return new OpenApi(
            info: $this->getInfo(),
            servers: $servers,
            paths: $this->paths,
            components: new Components(schemas: $this->schemas, securitySchemes: new ArrayObject($securitySchemes)),
            security: $securityRequirements
        );
    }

    /**
     * @param mixed[]                                         $responses
     * @param array{description: string, schema: string}|null $requestBody
     */
    private function addPath(string $id, string $path, string $tag, array $responses, string $description, ?array $requestBody = null): self {
        $apiResponses = collect($responses)
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
            ->put(400, new Response('Bad request'))
            ->put(405, new Response('Method Not Allowed'))
            ->put(500, new Response('Internal Server Error'));
        if (str_contains($path, 'logout')) {
            $apiResponses
                ->put(401, new Response('Unauthorized'))
                ->put(403, new Response('Forbidden'));
        }
        $this->paths->addPath($path, new PathItem(
            post: new Operation(
                operationId: $id,
                tags: [$tag],
                responses: $apiResponses->all(),
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

    #[Pure]
    private function getContact(): ?Contact {
        return $this->openApiOptions->getContactUrl() === null || $this->openApiOptions->getContactEmail() === null
            ? null
            : new Contact(
                name: $this->openApiOptions->getContactName(),
                url: $this->openApiOptions->getContactUrl(),
                email: $this->openApiOptions->getContactEmail()
            );
    }

    #[Pure]
    private function getInfo(): Info {
        return new Info(
            title: $this->openApiOptions->getTitle(),
            version: $this->openApiOptions->getVersion(),
            description: trim($this->openApiOptions->getDescription()),
            termsOfService: $this->openApiOptions->getTermsOfService(),
            contact: $this->getContact(),
            license: $this->getLicense()
        );
    }

    #[Pure]
    private function getLicense(): ?License {
        return $this->openApiOptions->getLicenseName() === null
            ? null
            : new License($this->openApiOptions->getLicenseName(), $this->openApiOptions->getLicenseUrl());
    }

    private function getLogin(): string {
        return $this->urlGenerator->generate('login');
    }

    private function getOauthSecurityScheme(): SecurityScheme {
        $oauthFlow = new OAuthFlow(
            authorizationUrl: $this->openApiOptions->getOAuthAuthorizationUrl(),
            tokenUrl: $this->openApiOptions->getOAuthTokenUrl(),
            refreshUrl: $this->openApiOptions->getOAuthRefreshUrl(),
            scopes: new ArrayObject($this->openApiOptions->getOAuthScopes())
        );
        $description = sprintf(
            'OAuth 2.0 %s Grant',
            strtolower(preg_replace('/[A-Z]/', ' \\0', lcfirst($this->openApiOptions->getOAuthFlow() ?? '')) ?? '')
        );
        $implicit = $password = $clientCredentials = $authorizationCode = null;
        switch ($this->openApiOptions->getOAuthFlow()) {
            case 'implicit':
                $implicit = $oauthFlow;
                break;
            case 'password':
                $password = $oauthFlow;
                break;
            case 'application':
            case 'clientCredentials':
                $clientCredentials = $oauthFlow;
                break;
            case 'accessCode':
            case 'authorizationCode':
                $authorizationCode = $oauthFlow;
                break;
            default:
                throw new LogicException('OAuth flow must be one of: implicit, password, clientCredentials, authorizationCode');
        }
        return new SecurityScheme(
            type: $this->openApiOptions->getOAuthType(),
            description: $description,
            name: null,
            in: null,
            scheme: null,
            bearerFormat: null,
            flows: new OAuthFlows($implicit, $password, $clientCredentials, $authorizationCode),
            openIdConnectUrl: null
        );
    }

    /**
     * @return array<string, SecurityScheme>
     */
    private function getSecuritySchemes(): array {
        $securitySchemes = ['cookieAuth' => new SecurityScheme(type: 'apiKey', name: 'PHPSESSID', in: 'cookie')];
        if ($this->openApiOptions->getOAuthEnabled()) {
            $securitySchemes['oauth'] = $this->getOauthSecurityScheme();
        }
        foreach ($this->openApiOptions->getApiKeys() as $key => $apiKey) {
            $description = sprintf('Value for the %s %s parameter.', $apiKey['name'], $apiKey['type']);
            $securitySchemes[$key] = new SecurityScheme('apiKey', $description, $apiKey['name'], $apiKey['type']);
        }
        return $securitySchemes;
    }

    private function setSecurityPaths(): void {
        $this->schemas['Auth'] = new ArrayObject([
            'properties' => [
                'password' => [
                    'description' => 'mot de passe',
                    'example' => 'super',
                    'type' => 'string',
                ],
                'username' => [
                    'description' => 'identifiant',
                    'example' => 'super',
                    'type' => 'string',
                ],
            ],
            'type' => 'object'
        ]);
        $this
            ->addPath(
                id: 'login',
                path: $this->getLogin(),
                tag: 'Auth',
                responses: [
                    200 => [
                        'description' => 'Utilisateur connecté',
                        'schema' => ['tag' => 'Employee', 'value' => 'Employee-read']
                    ]
                ],
                description: 'Connexion',
                requestBody: ['description' => 'Identifiants', 'schema' => 'Auth']
            )
            ->addPath(
                id: 'logout',
                path: '/api/logout',
                tag: 'Auth',
                responses: [204 => ['description' => 'Déconnexion réussie']],
                description: 'Déconnexion'
            );
    }
}
