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
use ApiPlatform\Core\OpenApi\Model\Paths;
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

final class OpenApiFactory implements OpenApiFactoryInterface {
    public const BASE_URL = 'base_url';

    /**
     * @param array<string, string[]> $formats
     */
    #[Pure]
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
        private Options $openApiOptions
    ) {
    }

    /**
     * @param array{base_url?: string} $context
     */
    public function __invoke(array $context = []): OpenApi {
        $baseUrl = $context[self::BASE_URL] ?? '/';
        $links = new Links();
        $paths = new Paths();
        $schemas = new ArrayObject();
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
            ))->collect($paths, $schemas);
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
            ))->collect($paths, $schemas);
        }

        $securitySchemes = $this->getSecuritySchemes();
        $securityRequirements = [];
        foreach (array_keys($securitySchemes) as $key) {
            $securityRequirements[] = [$key => []];
        }

        return new OpenApi(
            info: $this->getInfo(),
            servers: $servers,
            paths: $paths,
            components: new Components(schemas: $schemas, securitySchemes: new ArrayObject($securitySchemes)),
            security: $securityRequirements
        );
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
}
