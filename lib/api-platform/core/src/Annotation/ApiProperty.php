<?php

namespace App\ApiPlatform\Core\Annotation;

use ApiPlatform\Core\Annotation\ApiProperty as ApiPlatformProperty;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @phpstan-type OpenApiProperty array{readOnly: bool, type: string}
 */
final class ApiProperty extends ApiPlatformProperty {
    public function __construct(public readonly bool $readOnly = false, bool $required = false) {
        parent::__construct(required: $required);
    }

    /**
     * @return OpenApiProperty
     */
    #[ArrayShape(['readOnly' => 'bool', 'type' => 'string'])]
    public function toOpenApi(): array {
        return ['readOnly' => $this->readOnly, 'type' => 'string'];
    }
}
