<?php

namespace App\OpenApi;

final class Schema {
    /**
     * @param array<string, mixed> $properties
     * @param string[]             $required
     */
    public function __construct(
        private ?string $description = null,
        private array $properties = [],
        private array $required = [],
        private ?string $type = null
    ) {
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * @return array<string, mixed>
     */
    public function getProperties(): array {
        return $this->properties;
    }

    /**
     * @return string[]
     */
    public function getRequired(): array {
        return $this->required;
    }

    public function getType(): ?string {
        return $this->type;
    }
}
