<?php

namespace App\Fixtures;

use Doctrine\ORM\Mapping\ClassMetadata;

final class EntityConfig {
    /** @var array<string, PropertyConfig> */
    private array $properties;

    /**
     * @param ClassMetadata<object> $metadata
     * @param mixed[]               $properties
     */
    public function __construct(private ClassMetadata $metadata, array $properties) {
        $this->properties = collect($properties)
            ->map(static fn (array $config, string $property): PropertyConfig => new PropertyConfig($property, $config))
            ->all();
    }
}
