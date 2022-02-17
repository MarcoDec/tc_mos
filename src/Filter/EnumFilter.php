<?php

namespace App\Filter;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\QueryBuilder;
use Illuminate\Support\Collection;
use ReflectionProperty;

final class EnumFilter extends AbstractFilter {
    /**
     * @return mixed[]
     */
    public function getDescription(string $resourceClass): array {
        $description = [];
        $metadata = $this->getClassMetadata($resourceClass);
        $reflClass = $metadata->getReflectionClass();
        foreach ($this->properties as $property => $strategy) {
            $description[$property] = [
                'property' => $property,
                'required' => false,
                'schema' => [
                    'enum' => $this->getEnum($reflClass->getProperty($property)),
                    'type' => $this->getType($metadata->getTypeOfField($property))
                ]
            ];
        }
        return $description;
    }

    /**
     * @param mixed $value
     */
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?string $operationName = null): void {
        if (!$this->isPropertyEnabled($property, $resourceClass) || !$this->isPropertyMapped($property, $resourceClass)) {
            return;
        }

        $parameter = $queryNameGenerator->generateParameterName($property);
        $queryBuilder
            ->andWhere("{$queryBuilder->getRootAliases()[0]}.$property = :$parameter")
            ->setParameter($parameter, $value);
    }

    /**
     * @return mixed[]
     */
    private function getEnum(ReflectionProperty $property): array {
        /** @var Collection<int, mixed> $enum */
        $enum = new Collection();
        foreach ($property->getAttributes(ApiProperty::class) as $attribute) {
            /** @var ApiProperty $apiProperty */
            $apiProperty = $attribute->newInstance();
            if (
                !empty($apiProperty->attributes)
                && isset($apiProperty->attributes['openapi_context'])
                && !empty($context = $apiProperty->attributes['openapi_context'])
                && isset($context['enum'])
                && !empty($context['enum'])
            ) {
                $enum = $enum->merge($context['enum']);
            }
        }
        return $enum->values()->all();
    }

    private function getType(?string $doctrineType): string {
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
