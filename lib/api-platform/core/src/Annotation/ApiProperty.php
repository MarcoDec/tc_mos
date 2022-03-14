<?php

namespace App\ApiPlatform\Core\Annotation;

use ApiPlatform\Core\Annotation\ApiProperty as ApiPlatformProperty;
use App\ApiPlatform\Core\OpenApi\Factory\OpenApiContext;
use App\ApiPlatform\Core\OpenApi\Factory\Schema;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @phpstan-import-type OpenApiProperty from OpenApiContext
 */
final class ApiProperty extends ApiPlatformProperty implements OpenApiContext {
    /**
     * @param string[]                  $enum
     * @param array<ApiProperty|Schema> $oneOf
     */
    public function __construct(
        public readonly array $enum = [],
        public readonly array $oneOf = [],
        public readonly bool $readOnly = false,
        bool $required = false
    ) {
        parent::__construct(writable: !$this->readOnly, required: $required, openapiContext: $this->toOpenApi());
    }

    /**
     * @return OpenApiProperty
     */
    #[ArrayShape(['oneOf' => 'array', 'readOnly' => 'bool', 'type' => 'string'])]
    public function getOpenApiContext(): array {
        return $this->attributes['openapi_context'];
    }

    /**
     * @return OpenApiProperty
     */
    #[ArrayShape(['enum' => 'array', 'oneOf' => 'mixed', 'readOnly' => 'bool', 'type' => 'string'])]
    private function toOpenApi(): array {
        $context = ['readOnly' => $this->readOnly];
        if (!empty($this->enum)) {
            $context['enum'] = $this->enum;
        }
        if (empty($this->oneOf)) {
            $context['type'] = 'string';
        } else {
            $context['oneOf'] = collect($this->oneOf)
                ->map(static function (Schema|ApiProperty $one): array {
                    if ($one instanceof self) {
                        $context = $one->getOpenApiContext();
                        unset($context['readOnly']);
                        return $context;
                    }
                    return $one->getOpenApiContext();
                })
                ->values()
                ->all();
        }
        return $context;
    }
}
