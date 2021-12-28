<?php

namespace App\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\QueryBuilder;

class RelationFilter extends AbstractFilter {
    /**
     * @return mixed[]
     */
    public function getDescription(string $resourceClass): array {
        $description = [];
        $metadata = $this->getClassMetadata($resourceClass);
        $properties = $this->getProperties();

        if (null !== $properties) {
            foreach ($properties as $property => $value) {
                // Embedded classes should be checked there too as we're going further in the relation
                /** @phpstan-ignore-next-line */
                if (!$metadata->hasAssociation($property) && !array_key_exists($property, $metadata->embeddedClasses)) {
                    continue;
                }

                $propertyName = $this->normalizePropertyName($property);

                // If $value is an array, it means we passed other options [0 => value, 'required': true/false]
                $isFilterRequired = false;
                if (!is_array($value)) {
                    $field = $propertyName.'.'.$value;
                } else {
                    $field = $propertyName.'.'.$value[0];
                    $isFilterRequired = true;
                }

                // Only add the description if the Type exists (if it's null, then we've got an error in the value name as it's not a property of the object)
                if (null !== $this->getDoctrineFieldType($field, $resourceClass)) {
                    $description[$propertyName] = [
                        'property' => $value,
                        'type' => $this->getDoctrineFieldType($field, $resourceClass),
                        'required' => $isFilterRequired
                    ];
                } else {
                    $this->getLogger()->notice('Invalid filter ignored', [
                        'exception' => new InvalidArgumentException(sprintf('Invalid value for "%s" property', $property)),
                    ]);
                }
            }
        }

        return $description;
    }

    protected function filterProperty(string $property, mixed $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?string $operationName = null): void {
        $metadata = $this->getClassMetadata($resourceClass);
        $properties = $this->getProperties() ?? [];

        // Embedded classes should be checked there too as we're going further in the relation
        /** @phpstan-ignore-next-line */
        if ($metadata->hasAssociation($property) || array_key_exists($property, $metadata->embeddedClasses)) {
            $alias = $queryBuilder->getRootAliases()[0];
            $field = $property;

            if (!is_array($properties[$property])) {
                $propertyName = $properties[$property];
            } else {
                $propertyName = $properties[$property][0];
            }

            $nested = $field.'.'.$propertyName;

            if ($this->isPropertyNested($nested, $resourceClass)) {
                dump('ok');
                [$alias, $field] = $this->addJoinsForNestedProperty($nested, $alias, $queryBuilder, $queryNameGenerator, $resourceClass);

                $queryBuilder
                    ->andWhere(sprintf('%s.%s = :%s', $alias, $field, $propertyName))
                    ->setParameter(sprintf('%s', $propertyName), $value);
            } else {
                $queryBuilder
                    ->andWhere(sprintf('%s.%s.%s = :%s', $alias, $field, $propertyName, $propertyName))
                    ->setParameter(sprintf('%s', $propertyName), $value);
            }

            dump($queryBuilder);
        }
    }
}
