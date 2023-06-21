<?php

namespace App\Filter;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Collection;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\QueryBuilder;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;

/**
 * @phpstan-type FilterSchema array{enum: string[], type: string}
 * @phpstan-type FilterSchemaArray array{items: FilterSchema, type: 'array'}
 */
class EnumFilter extends AbstractFilter {
    /**
     * @param ReflectionClass<object> $class
     */
    final protected static function getReflectionProperty(ReflectionClass $class, string $property): ReflectionProperty {
        $matches = [];
        if (preg_match('/(\w+)\.(.+)/', $property, $matches) === 1) {
            /** @var ReflectionNamedType $type */
            $type = $class->getProperty($matches[1])->getType();
            /** @var class-string<object> $name */
            $name = $type->getName();
            return self::getReflectionProperty(new ReflectionClass($name), $matches[2]);
        }
        return $class->getProperty($property);
    }

    /**
     * @return array<string, array{property: string, required: bool, schema: FilterSchema|FilterSchemaArray}>
     */
    public function getDescription(string $resourceClass): array {
        $description = [];
        $metadata = $this->getClassMetadata($resourceClass);
        $reflClass = $metadata->getReflectionClass();
        foreach ($this->properties as $property => $strategy) {
            /** @var string $property */
            $description[$property] = [
                'property' => $property,
                'required' => false,
                'schema' => [
                    'enum' => $this->getEnum(self::getReflectionProperty($reflClass, $property)),
                    'type' => $this->getType($metadata->getTypeOfField($property))
                ]
            ];
        }
        return $description;
    }

    /**
     * @param string|string[] $value
     */
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?string $operationName = null): void {
        if (!$this->isPropertyEnabled($property, $resourceClass) || !$this->isPropertyMapped($property, $resourceClass)) {
            return;
        }

        if ($property === 'supplier') {
            $alias = $queryBuilder->getRootAliases()[0];
            $queryBuilder
                ->andWhere("$alias.id = :supplierId")
                ->setParameter('supplierId', $value->getSupplier());
        }
    }

    /**
     * @return string[]
     */
    final protected function getEnum(ReflectionProperty $property): array {
        /** @var Collection<int, string> $enum */
        $enum = new Collection();
        foreach ($property->getAttributes(ApiProperty::class) as $attribute) {
            /** @var ApiProperty $apiProperty */
            $apiProperty = $attribute->newInstance();
            if (
                !empty($apiProperty->attributes)
                && isset($apiProperty->attributes['openapi_context'])
                && !empty($context = $apiProperty->attributes['openapi_context'])
            ) {
                /** @var array{enum?: array<int, string>, items?: array{enum?: array<int, string>}} $context */
                if (isset($context['enum']) && !empty($context['enum'])) {
                    $enum = $enum->merge($context['enum']);
                } elseif (isset($context['items']['enum']) && !empty($context['items']['enum'])) {
                    $enum = $enum->merge($context['items']['enum']);
                }
            }
        }
        return $enum->all();
    }

    final protected function getType(?string $doctrineType): string {
        return match ($doctrineType) {
            Types::ARRAY => 'array',
            Types::BIGINT, Types::INTEGER, Types::SMALLINT => 'int',
            Types::BOOLEAN => 'bool',
            Types::DATE_MUTABLE, Types::TIME_MUTABLE, Types::DATETIME_MUTABLE, Types::DATETIMETZ_MUTABLE, Types::DATE_IMMUTABLE, Types::TIME_IMMUTABLE, Types::DATETIME_IMMUTABLE, Types::DATETIMETZ_IMMUTABLE => DateTimeInterface::class,
            Types::FLOAT => 'float',
            default => 'string',
        };
    }
}
