<?php

declare(strict_types=1);

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter as OrmSearchFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Query\Expr\From;
use Doctrine\ORM\QueryBuilder;
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
        $properties = $this->getProperties();
        if (isset($properties['type']) && isset($description['type']) === false) {
            foreach ($refl->getAttributes(ORM\DiscriminatorMap::class) as $attribute) {
                /** @var ORM\DiscriminatorMap $instance */
                $instance = $attribute->newInstance();
                $description['type'] = [
                    'is_collection' => false,
                    'property' => 'type',
                    'required' => false,
                    'schema' => ['enum' => array_keys($instance->value), 'type' => 'string'],
                    'strategy' => $properties['type'],
                    'type' => 'string'
                ];
            }
        }
        return $description;
    }

    /**
     * @param mixed   $value
     * @param mixed[] $context
     */
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void {
        parent::filterProperty($property, $value, $queryBuilder, $queryNameGenerator, $resourceClass, $operation, $context);
        if (null === $value) {
            return;
        }
        if (
            $property === 'type' && (
                $this->isPropertyEnabled($property, $resourceClass) === false
                || $this->isPropertyMapped($property, $resourceClass, true) === false
            )
        ) {
            $refl = $this->getClassMetadata($resourceClass)->getReflectionClass();
            foreach ($refl->getAttributes(ORM\DiscriminatorMap::class) as $attribute) {
                /** @var ORM\DiscriminatorMap $instance */
                $instance = $attribute->newInstance();
                $parts = $queryBuilder->getDQLParts();
                $queryBuilder->resetDQLParts();
                foreach ($parts as $name => $part) {
                    if ($name === 'from' && is_array($part) && $part[0] instanceof From) {
                        $queryBuilder->from($instance->value[$value], $part[0]->getAlias());
                    } elseif (empty($part) === false) {
                        /* @phpstan-ignore-next-line */
                        $queryBuilder->add($name, $part);
                    }
                }
            }
        }
    }
}
