<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\Api\IdentifiersExtractorInterface;
use ApiPlatform\Core\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Core\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Components;
use ApiPlatform\Core\OpenApi\Model\Contact;
use ApiPlatform\Core\OpenApi\Model\Info;
use ApiPlatform\Core\OpenApi\Model\License;
use ApiPlatform\Core\OpenApi\Model\Paths;
use ApiPlatform\Core\OpenApi\Model\Server;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Options;
use ApiPlatform\Core\PathResolver\OperationPathResolverInterface;
use App\OpenApi\Factory\ItemPaths;
use App\OpenApi\Factory\Links;
use ArrayObject;
use JetBrains\PhpStorm\Pure;

final class OpenApiFactory implements OpenApiFactoryInterface {
    public const BASE_URL = 'base_url';

    /**
     * @param array<string, string[]> $formats
     */
    #[Pure]
    public function __construct(
        private array $formats,
        private IdentifiersExtractorInterface $identifiersExtractor,
        private SchemaFactoryInterface $jsonSchemaFactory,
        protected OperationPathResolverInterface $operationPathResolver,
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
            $itemPaths = new ItemPaths(
                formats: $this->formats,
                identifiersExtractor: $this->identifiersExtractor,
                jsonSchemaFactory: $this->jsonSchemaFactory,
                links: $links,
                operationPathResolver: $this->operationPathResolver,
                propertyMetadataFactory: $this->propertyMetadataFactory,
                propertyNameCollectionFactory: $this->propertyNameCollectionFactory,
                resourceClass: $resourceClass,
                resourceMetadata: $this->resourceMetadataFactory->create($resourceClass)
            );
            $itemPaths->collect($paths, $schemas);
        }

        return new OpenApi(
            info: $this->getInfo(),
            servers: $servers,
            paths: $paths,
            components: new Components($schemas)
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
}
