<?php

namespace App\ApiPlatform\Core\Annotation;

use ApiPlatform\Core\Annotation\ApiProperty as ApiPlatformProperty;
use App\ApiPlatform\Core\OpenApi\Factory\OpenApiContext;
use App\ApiPlatform\Core\OpenApi\Factory\Schema;
use App\Validator as AppAssert;
use Attribute;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use ReflectionNamedType;
use ReflectionProperty;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @phpstan-import-type OpenApiProperty from OpenApiContext
 *
 * @phpstan-type PropertyDefault array<bool|float|int|string>|bool|float|int|string|null
 *
 * @property array{openapi_context: OpenApiProperty} $attributes
 */
#[Attribute]
final class ApiProperty extends ApiPlatformProperty implements OpenApiContext {
    private bool $readMode = true;

    /**
     * @param PropertyDefault           $default
     * @param string[]                  $enum
     * @param PropertyDefault           $example
     * @param array<ApiProperty|Schema> $oneOf
     */
    public function __construct(
        array|bool|float|int|string|null $default = null,
        public readonly array $enum = [],
        array|bool|float|int|string|null $example = null,
        ?string $format = null,
        public bool $nullable = true,
        public readonly array $oneOf = [],
        public readonly bool $readOnly = false,
        public readonly ?string $readRef = null,
        public readonly ?string $ref = null,
        bool $required = false,
        public readonly ?string $writeRef = null
    ) {
        parent::__construct(
            writable: !$this->readOnly,
            required: $required,
            default: $default,
            example: $example
        );
        $this->attributes['openapi_context'] = $this->toOpenApi();
        if ($this->hasNoRef()) {
            $this->setDefault($default);
            $this->setFormat($format);
        }
    }

    private static function getReflDescription(ReflectionProperty $refl): ?string {
        if (!empty($doc = $refl->getDocComment())) {
            $matches = [];
            preg_match('/@var\s\w+[\|\w+]*\s(.*)/m', $doc, $matches);
            return isset($matches[1]) && !empty($matches[1]) ? $matches[1] : null;
        }
        return null;
    }

    /**
     * @return OpenApiProperty
     */
    public function getOpenApiContext(): array {
        return $this->attributes['openapi_context'];
    }

    #[Pure]
    public function hasRef(): bool {
        return !$this->hasNoRef();
    }

    public function setReadMode(bool $readMode): self {
        $this->readMode = $readMode;
        if ($this->hasRef()) {
            $this->attributes['openapi_context'] = $this->toOpenApi();
        }
        return $this;
    }

    public function setReflectionProperty(ReflectionProperty $property): self {
        if ($this->hasRef()) {
            return $this;
        }

        /** @var PropertyDefault $default */
        $default = $property->getDefaultValue();
        $this->setDefault($default);

        if (!empty($description = self::getReflDescription($property))) {
            $this->description = $description;
            $this->attributes['openapi_context']['description'] = $description;
            $this->attributes['openapi_context']['title'] = $description;
        }

        if (count($property->getAttributes(Assert\Email::class)) === 1) {
            $this->setFormat('email');
        } elseif (count($property->getAttributes(AppAssert\PhoneNumber::class)) === 1) {
            $this->setFormat('telephone');
        } elseif (count($property->getAttributes(AppAssert\ZipCode::class)) === 1) {
            $this->setFormat('postalCode');
        }

        $lengthAttributes = $property->getAttributes(Assert\Length::class);
        if (count($lengthAttributes) === 1) {
            /** @var Assert\Length $length */
            $length = $lengthAttributes[0]->newInstance();
            $this->attributes['openapi_context']['maxLength'] = $length->max;
            $this->attributes['openapi_context']['minLength'] = $length->min;
        }

        $this->required = count($property->getAttributes(Assert\NotBlank::class)) === 1;
        if (($type = $property->getType()) instanceof ReflectionNamedType) {
            $this->nullable = $type->allowsNull()
                && count($property->getAttributes(ORM\Id::class)) === 0
                && !$this->required;
            $this->attributes['openapi_context']['nullable'] = $this->nullable;
            $this->attributes['openapi_context']['type'] = match ($name = $type->getName()) {
                DateTimeImmutable::class => 'string',
                'bool' => 'boolean',
                'int' => 'integer',
                default => $name
            };
        }
        return $this;
    }

    private function getRef(): ?string {
        return empty($this->ref)
            ? ($this->readMode ? $this->readRef : $this->writeRef)
            : $this->ref;
    }

    private function hasNoRef(): bool {
        return empty($this->ref) && empty($this->readRef) && empty($this->writeRef);
    }

    /**
     * @param PropertyDefault $default
     */
    private function setDefault(array|bool|float|int|string|null $default): void {
        $this->default = $default;
        if (
            $this->default !== null && (
                !isset($this->attributes['openapi_context']['enum'])
                || in_array($this->default, $this->attributes['openapi_context']['enum'])
            )
        ) {
            $this->attributes['openapi_context']['default'] = $this->default;
            $this->example = $this->default;
            $this->attributes['openapi_context']['example'] = $this->default;
        }
    }

    private function setFormat(?string $format): void {
        if (!empty($format)) {
            $this->attributes['openapi_context']['externalDocs'] = ['url' => "https://schema.org/$format"];
            $this->attributes['openapi_context']['format'] = $format;
        }
    }

    /**
     * @return OpenApiProperty
     */
    private function toOpenApi(): array {
        if ($this->hasRef()) {
            return ['$ref' => "#/components/schemas/{$this->getRef()}"];
        }

        $context = ['nullable' => $this->nullable, 'readOnly' => $this->readOnly];
        if (!empty($this->enum)) {
            $context['enum'] = $this->enum;
        }
        if ($this->example !== null) {
            $context['example'] = $this->example;
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
