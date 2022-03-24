<?php

namespace App\OpenApi;

use App\ApiPlatform\Core\Annotation\ApiProperty;
use Illuminate\Support\Collection;

/**
 * @phpstan-import-type OpenApiProperty from ApiProperty
 */
final class Schema {
    /** @var Collection<string, ApiProperty> */
    private readonly Collection $properties;

    /**
     * @param array<string, ApiProperty> $properties
     */
    public function __construct(private readonly string $description, array $properties) {
        $this->properties = collect($properties);
    }

    public function getDescription(): string {
        return $this->description;
    }

    /**
     * @return array<string, OpenApiProperty>
     */
    public function getProperties(): array {
        /** @var Collection<string, OpenApiProperty> $mapped */
        $mapped = $this->properties->map->toOpenApi();
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

    public function getType(): string {
        return 'object';
    }
}
