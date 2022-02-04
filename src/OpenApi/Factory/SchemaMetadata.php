<?php

namespace App\OpenApi\Factory;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use JetBrains\PhpStorm\Pure;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;

final class SchemaMetadata {
    /** @var ReflectionClass<Entity> */
    private ReflectionClass $refl;

    /**
     * @param class-string<Entity> $className
     */
    public function __construct(string $className) {
        $this->refl = new ReflectionClass($className);
    }

    /**
     * @return array<string, ApiProperty>
     */
    public function getMeasures(): array {
        $measures = [];
        foreach ($this->getProperties() as $property) {
            $type = $property->getType();
            if ($type instanceof ReflectionNamedType && $type->getName() === Measure::class) {
                $attributes = $property->getAttributes(ApiProperty::class);
                if (count($attributes) === 1) {
                    $measures[$property->getName()] = $attributes[0]->newInstance();
                }
            }
        }
        return $measures;
    }

    /**
     * @return ReflectionProperty[]
     */
    #[Pure]
    private function getProperties(): array {
        return $this->refl->getProperties();
    }
}
