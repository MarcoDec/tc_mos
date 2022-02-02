<?php

declare(strict_types=1);

namespace App\OpenApi\Factory;

use ApiPlatform\Core\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Core\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use ApiPlatform\Core\OpenApi\Model;
use ApiPlatform\Core\PathResolver\OperationPathResolverInterface;
use ArrayObject;

abstract class Paths {
    /**
     * @param array<string, string[]> $formats
     */
    public function __construct(
        protected array $formats,
        protected SchemaFactoryInterface $jsonSchemaFactory,
        protected Links $links,
        protected OperationPathResolverInterface $operationPathResolver,
        protected PropertyMetadataFactoryInterface $propertyMetadataFactory,
        protected PropertyNameCollectionFactoryInterface $propertyNameCollectionFactory,
        protected string $resourceClass,
        protected ResourceMetadata $resourceMetadata
    ) {
    }

    /**
     * @param mixed[] $operation
     */
    abstract protected function createOperation(array $operation, string $operationName): Operation;

    /**
     * @return mixed[]
     */
    abstract protected function getOperations(): array;

    /**
     * @param ArrayObject<string, mixed> $schemas
     */
    final public function collect(Model\Paths $paths, ArrayObject $schemas): void {
        $item = new Model\PathItem();
        $path = null;
        foreach ($this->getOperations() as $operationName => $operation) {
            if (($model = $this->createOperation($operation, $operationName))->isNotHidden()) {
                $item = $item->{'with'.ucfirst($model->getMethod())}($model->create($schemas));
                $path = $model->getPath();
            }
        }
        if (!empty($path)) {
            $paths->addPath($path, $item);
        }
    }
}
