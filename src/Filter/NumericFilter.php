<?php

namespace App\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\NumericFilter as ApiNumericFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\QueryBuilder;

class NumericFilter extends ApiNumericFilter {
    /**
     * @return mixed[]
     */
    public function getDescription(string $resourceClass): array {
        $description = [];

        $properties = $this->getProperties();
        if (null === $properties) {
            $properties = array_fill_keys($this->getClassMetadata($resourceClass)->getFieldNames(), null);
        }

        foreach ($properties as $property => $unused) {
            if (!$this->isPropertyMapped($property, $resourceClass) || !$this->isNumericField($property, $resourceClass)) {
                continue;
            }

            $propertyName = $this->normalizePropertyName($property);
            $description[$propertyName] = [
                'property' => $propertyName,
                'type' => $this->getType((string) $this->getDoctrineFieldType($property, $resourceClass)),
                'required' => false
            ];
        }

        return $description;
    }

    protected function emptyPropertyCondition(string $property, string $resourceClass): bool {
        return !$this->isPropertyEnabled($property, $resourceClass)
            || !$this->isPropertyMapped($property, $resourceClass)
            || !$this->isNumericField($property, $resourceClass);
    }

    /**
     * @param mixed $value
     */
    final protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?string $operationName = null): void {
        if ($this->emptyPropertyCondition($property, $resourceClass)) {
            return;
        }

        $values = $this->normalizeValues($value, $property);
        if (null === $values) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $field = $property;

        if ($this->isPropertyNested($property, $resourceClass)) {
            [$alias, $field] = $this->addJoinsForNestedProperty($property, $alias, $queryBuilder, $queryNameGenerator, $resourceClass);
        }

        $valueParameter = $queryNameGenerator->generateParameterName($field);

        $queryBuilder
            ->andWhere(sprintf('%s.%s = :%s', $alias, $field, $valueParameter))
            ->setParameter($valueParameter, $values[0], $this->getDoctrineFieldType($property, $resourceClass));
    }

    final protected function getDoctrineFieldType(string $property, string $resourceClass): string {
        $type = parent::getDoctrineFieldType($property, $resourceClass);
        if (empty($type)) {
            return Types::INTEGER;
        }
        if ($type instanceof Type) {
            return $type->getName();
        }
        return $type;
    }

    protected function isNumericField(string $property, string $resourceClass): bool {
        return parent::isNumericField($property, $resourceClass)
            || $this->getDoctrineFieldType($property, $resourceClass) === 'tinyint';
    }

    /**
     * @param mixed $value
     *
     * @return float[]|int[]|null
     */
    final protected function normalizeValues($value, string $property): ?array {
        if (!is_numeric($value)) {
            $this->getLogger()->notice('Invalid filter ignored', [
                'exception' => new InvalidArgumentException(sprintf('Invalid numeric value for "%s" property', $property)),
            ]);

            return null;
        }

        return [$value + 0]; // coerce $val to the right type.
    }
}
