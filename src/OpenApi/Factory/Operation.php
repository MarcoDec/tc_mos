<?php

declare(strict_types=1);

namespace App\OpenApi\Factory;

use ApiPlatform\Core\Api\OperationType;
use ApiPlatform\Core\JsonSchema\Schema;
use ApiPlatform\Core\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Core\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use ApiPlatform\Core\OpenApi\Model;
use ApiPlatform\Core\PathResolver\OperationPathResolverInterface;
use ArrayObject;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use LogicException;

abstract class Operation {
    /**
     * @var Schema<string, mixed>
     */
    private Schema $schema;

    /**
     * @param array<string, string[]> $formats
     * @param mixed[]                 $operation
     */
    public function __construct(
        private array $formats,
        private SchemaFactoryInterface $jsonSchemaFactory,
        private Links $links,
        protected array $operation,
        private string $operationName,
        private OperationPathResolverInterface $operationPathResolver,
        private PropertyMetadataFactoryInterface $propertyMetadataFactory,
        private PropertyNameCollectionFactoryInterface $propertyNameCollectionFactory,
        protected string $resourceClass,
        protected ResourceMetadata $resourceMetadata
    ) {
        $this->schema = new Schema('openapi');
    }

    /**
     * @param string[][] $responseFormats
     *
     * @return string[]
     */
    private static function flattenMimeTypes(array $responseFormats): array {
        $responseMimeTypes = [];
        foreach ($responseFormats as $responseFormat => $mimeTypes) {
            foreach ($mimeTypes as $mimeType) {
                $responseMimeTypes[$mimeType] = $responseFormat;
            }
        }
        return $responseMimeTypes;
    }

    /**
     * @return string[]
     */
    abstract protected function getIdentifiers(): array;

    /**
     * @return Model\Parameter[]
     */
    abstract protected function getParameters(): array;

    abstract protected function getResponseContent(): string;

    abstract protected function getType(): string;

    /**
     * @param ArrayObject<string, mixed> $schemas
     */
    final public function create(ArrayObject $schemas): Model\Operation {
        $this->schema->setDefinitions($schemas);
        $this->links->put($this->getOperationId(), $this->getLink());
        return new Model\Operation(
            operationId: $this->getOperationId(),
            tags: $this->getTags(),
            responses: $this->getResponses(),
            summary: $this->getSummary(),
            description: $this->getSummary('description'),
            externalDocs: $this->getExternalDocs(),
            parameters: $this->getParameters(),
            requestBody: $this->getRequestBody(),
            callbacks: $this->getCallbacks(),
            deprecated: $this->isDeprecated(),
            security: $this->getSecurity(),
            servers: $this->getServers(),
            extensionProperties: $this->getExtensionProperties()
        );
    }

    public function getMethod(): string {
        return $this->resourceMetadata->getTypedOperationAttribute(
            operationType: $this->getType(),
            operationName: $this->operationName,
            key: 'method',
            defaultValue: 'GET'
        );
    }

    public function getPath(): string {
        $path = removeEnd($this->operationPathResolver->resolveOperationPath(
            resourceShortName: $this->getShortName(),
            operation: $this->operation,
            operationType: $this->getType()
        ), '.{_format}');
        return str_starts_with($path, '/') ? $path : "/$path";
    }

    /**
     * @return Model\Response[]
     */
    protected function getResponses(): array {
        $method = $this->getMethod();
        $responses = [
            400 => new Model\Response('Bad request'),
            401 => new Model\Response('Unauthorized'),
            403 => new Model\Response('Forbidden'),
            405 => new Model\Response('Method Not Allowed'),
            500 => new Model\Response('Internal Server Error'),
        ];
        if ($method === 'GET') {
            $responses[$this->getSuccessStatus()] = new Model\Response(
                description: sprintf('%s %s', $this->getShortName(), $this->getResponseContent()),
                content: $this->buildContent()
            );
        } elseif (in_array($method, ['PATCH', 'POST', 'PUT'])) {
            $responses[$this->getSuccessStatus($method === 'POST' ? 201 : 200)] = new Model\Response(
                description: sprintf($method === 'POST' ? '%s resource created' : '%s resource updated', $this->getShortName()),
                content: $this->buildContent(),
                links: new ArrayObject($this->links->get($this->getLinkedOperationId()))
            );
            $responses[422] = new Model\Response('Unprocessable entity');
        } elseif ($method === 'DELETE') {
            $responses[$this->getSuccessStatus(204)] = new Model\Response(
                sprintf('%s resource deleted', $this->getShortName())
            );
        }
        if (isset($this->operation['openapi_context']['responses'])) {
            foreach ($this->operation['openapi_context']['responses'] as $status => $context) {
                $responses[$status] = new Model\Response(
                    description: $context['description'] ?? null,
                    content: isset($context['content']) ? new ArrayObject($context['content']) : null,
                    headers: isset($context['headers']) ? new ArrayObject($context['headers']) : null,
                    links: isset($context['links']) ? new ArrayObject($context['links']) : null
                );
            }
        }
        ksort($responses);
        return $responses;
    }

    /**
     * @param ArrayObject<string, mixed> $definitions
     */
    private function appendSchemaDefinitions(ArrayObject $definitions): void {
        $schemas = $this->schema->getDefinitions();
        foreach ($definitions as $key => $value) {
            $schemas[$key] = $value;
        }
    }

    /**
     * @return ArrayObject<string, mixed>
     */
    private function buildContent(string $type = Schema::TYPE_OUTPUT): ArrayObject {
        $content = new ArrayObject();
        $schemas = $this->buildSchemas($type);
        foreach ($this->getMimeTypes()['responseMimeTypes'] as $mime => $format) {
            $content[$mime] = new Model\MediaType(new ArrayObject($schemas[$format]->getArrayCopy(false)));
        }
        return $content;
    }

    /**
     * @return Schema<string, mixed>[]
     */
    private function buildSchemas(string $type = Schema::TYPE_OUTPUT): array {
        $schemas = [];
        foreach ($this->getMimeTypes()['responseMimeTypes'] as $format) {
            $schema = $this->jsonSchemaFactory->buildSchema(
                className: $this->resourceClass,
                format: $format,
                type: $type,
                operationType: $this->getType(),
                operationName: $this->operationName,
                schema: $this->schema,
                forceCollection: false
            );
            $schemas[$format] = $schema;
            $this->appendSchemaDefinitions($schema->getDefinitions());
        }
        return $schemas;
    }

    /**
     * @return ArrayObject<int|string, mixed>|null
     */
    private function getCallbacks(): ?ArrayObject {
        return isset($this->operation['openapi_context']['callbacks'])
            ? new ArrayObject($this->operation['openapi_context']['callbacks'])
            : null;
    }

    /**
     * @return mixed[]
     */
    private function getExtensionProperties(): array {
        return array_filter(
            array: $this->operation['openapi_context'] ?? [],
            callback: static fn (string $item): bool => preg_match('/^x-.*$/i', $item) !== false,
            mode: ARRAY_FILTER_USE_KEY
        );
    }

    #[Pure]
    private function getExternalDocs(): ?Model\ExternalDocumentation {
        return isset($this->operation['openapi_context']['externalDocs'])
            ? new Model\ExternalDocumentation(
                description: $this->operation['openapi_context']['externalDocs']['description'] ?? null,
                url: $this->operation['openapi_context']['externalDocs']['url']
            )
            : null;
    }

    private function getLink(): Model\Link {
        $parameters = [];
        foreach ($this->propertyNameCollectionFactory->create($this->resourceClass) as $property) {
            if ($this->propertyMetadataFactory->create($this->resourceClass, $property)->isIdentifier()) {
                $parameters[$property] = '$response.body#/'.$property;
            }
        }
        $path = $this->getPath();
        return new Model\Link(
            operationId: $this->getOperationId(),
            parameters: new ArrayObject($parameters),
            description: count($parameters) === 1
                ? sprintf(
                    'The `%1$s` value returned in the response can be used as the `%1$s` parameter in `GET %2$s`.',
                    key($parameters),
                    $path
                )
                : sprintf('The values returned in the response can be used in `GET %s`.', $path)
        );
    }

    private function getLinkedOperationId(): string {
        return 'get'.ucfirst($this->getShortName()).ucfirst(OperationType::ITEM);
    }

    /**
     * @return array{requestMimeTypes: string[], responseMimeTypes: string[]}
     */
    #[ArrayShape(['requestMimeTypes' => 'string[]', 'responseMimeTypes' => 'string[]'])]
    private function getMimeTypes(): array {
        return [
            'requestMimeTypes' => self::flattenMimeTypes($this->resourceMetadata->getTypedOperationAttribute(
                operationType: $this->getType(),
                operationName: $this->operationName,
                key: 'input_formats',
                defaultValue: $this->formats,
                resourceFallback: true
            )),
            'responseMimeTypes' => self::flattenMimeTypes($this->resourceMetadata->getTypedOperationAttribute(
                operationType: $this->getType(),
                operationName: $this->operationName,
                key: 'output_formats',
                defaultValue: $this->formats,
                resourceFallback: true
            ))
        ];
    }

    private function getOperationId(): string {
        return $this->operation['openapi_context']['operationId']
            ?? lcfirst($this->operationName).ucfirst($this->getShortName()).ucfirst($this->getType());
    }

    private function getRequestBody(): ?Model\RequestBody {
        $method = $this->getMethod();
        $description = sprintf(
            'The %s %s resource',
            $method === 'POST' ? 'new' : 'updated',
            $this->getShortName()
        );
        return match (true) {
            !empty($body = $this->operation['openapi_context']['requestBody'] ?? false) => new Model\RequestBody(
                description: $body['description'] ?? $description,
                content: isset($body['context']) ? new ArrayObject($body['context']) : null,
                required: $body['required'] ?? false
            ),
            in_array($method, ['PATCH', 'POST', 'PUT']) => new Model\RequestBody(
                description: $description,
                content: $this->buildContent(Schema::TYPE_INPUT),
                required: true
            ),
            default => null
        };
    }

    /**
     * @return mixed[]|null
     */
    private function getSecurity(): ?array {
        return $this->operation['openapi_context']['security'] ?? null;
    }

    /**
     * @return mixed[]|null
     */
    private function getServers(): ?array {
        return $this->operation['openapi_context']['servers'] ?? null;
    }

    private function getShortName(): string {
        if (empty($shortName = $this->resourceMetadata->getShortName())) {
            throw new LogicException('ShortName not found.');
        }
        return $shortName;
    }

    private function getSuccessStatus(int $defaultValue = 200): int {
        return $this->resourceMetadata->getTypedOperationAttribute($this->getType(), $this->operationName, 'status', $defaultValue);
    }

    private function getSummary(string $key = 'summary'): string {
        return $this->operation['openapi_context'][$key] ?? sprintf(match ($this->getMethod()) {
            'DELETE' => 'Removes the %s resource.',
                'GET' => 'Retrieves a %s resource.',
                'PATCH' => 'Updates the %s resource.',
                'POST' => 'Creates a %s resource.',
                'PUT' => 'Replaces the %s resource.',
                default => '%s'
        }, $this->getShortName());
    }

    /**
     * @return string[]
     */
    private function getTags(): array {
        return $this->operation['openapi_context']['tags'] ?? [$this->getShortName()];
    }

    private function isDeprecated(): bool {
        return $this->operation['openapi_context']['deprecated']
            ?? $this->resourceMetadata->getTypedOperationAttribute(
                operationType: $this->getType(),
                operationName: $this->operationName,
                key: 'deprecation_reason',
                defaultValue: false,
                resourceFallback: true
            );
    }
}
