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

    /** @var string[] */
    private array $required = [];

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
     * @param string[] $required
     */
    public function appendRequired(array $required): self {
        $this->required = array_merge($this->required, $required);
        return $this;
    }

    /**
     * @return string[]
     */
    public function getNotRequired(): array {
        $required = $this->getRequired();
        return $this->getProperties()
            ->keys()
            ->filter(static fn (string $property): bool => !in_array($property, $required))
            ->values()
            ->all();
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
            $context['properties'] = $this->getProperties()->all();
            $context['required'] = $this->getRequired();
        }
        return $context;
    }

    /**
     * @return string[]
     */
    public function getParents(): array {
        return collect($this->allOf)->filter(static fn (self|string $all): bool => is_string($all))->values()->all();
    }

    /**
     * @return Collection<string, OpenApiProperty>
     */
    private function getProperties(): Collection {
        /** @phpstan-ignore-next-line */
        return $this->properties->map->getOpenApiContext();
    }

    /**
     * @return string[]
     */
    private function getRequired(): array {
        if (empty($this->required)) {
            /** @var Collection<string, ApiProperty> $required */
            $required = $this->properties->filter->required;
            $this->required = $required->keys()->values()->all();
        }
        return $this->required;
    }
}
