<?php

namespace App\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\QueryBuilder;

final class RelationFilter extends NumericFilter {
    /**
     * @return mixed[]
     */
    public function getDescription(string $resourceClass): array {
        $description = [];
        $metadata = $this->getClassMetadata($resourceClass);

        $properties = $this->getProperties();
        if (null === $properties) {
            $properties = array_fill_keys($this->getClassMetadata($resourceClass)->getFieldNames(), null);
        }

        foreach ($properties as $property => $unused) {
            if (!$metadata->hasAssociation($property)) {
                continue;
            }

            $propertyName = $this->normalizePropertyName($property);
            $description[$propertyName] = [
                'property' => $propertyName,
                'type' => $this->getType((string) $this->getDoctrineFieldType($property, $resourceClass)),
                'required' => false,
            ];
        }

        return $description;
    }

    /**
     * @param mixed $value
     */
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?string $operationName = null): void {
        $metadata = $this->getClassMetadata($resourceClass);
        if (!$metadata->hasAssociation($property)) {
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
            ->setParameter($valueParameter, $values[0], (string) $this->getDoctrineFieldType($property, $resourceClass));
    }

    /**
     * @param mixed $value
     *
     * @return float[]|int[]|null
     */
    protected function normalizeValues($value, string $property): ?array {
        if (!is_numeric($value)) {
            $this->getLogger()->notice('Invalid filter ignored', [
                'exception' => new InvalidArgumentException(sprintf('Invalid numeric value for "%s" property', $property)),
            ]);

            return null;
        }

        return [$value + 0]; // coerce $val to the right type.
    }
}
