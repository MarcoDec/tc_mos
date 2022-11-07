<?php

declare(strict_types=1);

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter as OrmSearchFilter;
use ApiPlatform\Metadata\ApiProperty;
use ReflectionAttribute;

class SearchFilter extends OrmSearchFilter {
    /** @return mixed[] */
    public function getDescription(string $resourceClass): array {
        $description = parent::getDescription($resourceClass);
        $refl = $this->getClassMetadata($resourceClass)->getReflectionClass();
        foreach ($description as $property => &$definition) {
            if ($definition['strategy'] !== 'exact') {
                continue;
            }
            if (str_ends_with($property, '[]')) {
                $property = mb_substr($property, 0, -2);
            }
            /** @param ReflectionAttribute $attributes */
            $define = static function (array $attributes) use ($definition) {
                foreach ($attributes as $attribute) {
                    /** @var ApiProperty $instance */
                    $instance = $attribute->newInstance();
                    if (isset($instance->getOpenapiContext()['enum'])) {
                        $schema = [
                            'enum' => $instance->getOpenapiContext()['enum'],
                            'type' => $definition['type']
                        ];
                        $definition['schema'] = $definition['is_collection']
                            ? ['items' => $schema, 'type' => 'array']
                            : $schema;
                        return $definition;
                    }
                }
                return $definition;
            };
            $definition = $define([
                ...$refl->getProperty($property)->getAttributes(ApiProperty::class),
                ...$refl->getMethod('get'.ucfirst($property))->getAttributes(ApiProperty::class),
                ...$refl->getMethod('set'.ucfirst($property))->getAttributes(ApiProperty::class)
            ]);
        }
        return $description;
    }
}
