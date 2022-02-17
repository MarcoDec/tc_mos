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
                // Embedded classes should be checked there too as we going further in the relation
                /** @phpstan-ignore-next-line */
                if (!$metadata->hasAssociation($property) && !array_key_exists($property, $metadata->embeddedClasses)) {
                    continue;
                }

                $propertyName = $this->normalizePropertyName($property);

                // Only add the description if the Type exists (if it's null, then we've got an error in the value name as it's not a property of the object)
                if (null !== $this->getDoctrineFieldType($value, $resourceClass)) {
                    $description[$propertyName] = [
                        'property' => $value,
                        'type' => $this->getDoctrineFieldType($value, $resourceClass),
                        'required' => false
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

        // Embedded classes should be checked there too as we going further in the relation
        /** @phpstan-ignore-next-line */
        if ($metadata->hasAssociation($property) || array_key_exists($property, $metadata->embeddedClasses)) {
            $alias = $queryBuilder->getRootAliases()[0];
            $field = $property;

            if (array_key_exists($property, $properties)) {
                $queryBuilder
                    ->andWhere(sprintf('%s.%s.%s = :%s', $alias, $field, $properties[$property], $properties[$property]))
                    ->setParameter(sprintf('%s', $properties[$property]), $value);
            }
        }
    }
}
