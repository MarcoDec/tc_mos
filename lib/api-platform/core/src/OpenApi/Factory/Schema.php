<?php

namespace App\ApiPlatform\Core\OpenApi\Factory;

use App\ApiPlatform\Core\Annotation\ApiProperty;
use function collect;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @phpstan-import-type OpenApiProperty from OpenApiContext
 * @phpstan-import-type SchemaContext from OpenApiContext
 */
final class Schema implements OpenApiContext {
    /** @var Collection<string, ApiProperty> */
    private readonly Collection $properties;

    /**
     * @param array<self|string>         $allOf
     * @param array<string, ApiProperty> $properties
     */
    public function __construct(
        private readonly bool $additionalProperties = false,
        private readonly array $allOf = [],
        private readonly ?string $description = null,
        private readonly ?bool $nullable = null,
        array $properties = []
    ) {
        $this->properties = collect($properties);
    }

    /**
     * @return SchemaContext
     */
    #[ArrayShape([
        'additionalProperties' => 'bool',
        'allOf' => 'mixed',
        'description' => 'null|string',
        'nullable' => 'bool',
        'properties' => 'mixed',
        'required' => 'string[]',
        'type' => 'string'
    ])]
    public function getOpenApiContext(): array {
        $context = ['additionalProperties' => $this->additionalProperties, 'type' => 'object'];
        if (!empty($this->allOf)) {
            $context['allOf'] = collect($this->allOf)
                ->map(static fn (self|string $all): array => $all instanceof self ? $all->getOpenApiContext() : ['$ref' => "#/components/schemas/$all"])
                ->values()
                ->all();
        }
        if (!empty($this->description)) {
            $context['description'] = $this->description;
        }
        if ($this->nullable !== null) {
            $context['nullable'] = $this->nullable;
        }
        if (!empty($this->properties)) {
            $context['properties'] = $this->getProperties();
            $context['required'] = $this->getRequired();
        }
        return $context;
    }

    /**
     * @return array<string, OpenApiProperty>
     */
    public function getProperties(): array {
        /** @var Collection<string, OpenApiProperty> $mapped */
        $mapped = $this->properties->map->getOpenApiContext();
        return $mapped->all();
    }

    /**
     * @return string[]
     */
    public function getRequired(): array {
        /** @var Collection<string, ApiProperty> $required */
        $required = $this->properties->filter->required;
        return $required->keys()->all();
    }
}
