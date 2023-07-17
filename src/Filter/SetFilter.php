<?php

namespace App\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Doctrine\DBAL\Connection;
use Doctrine\ORM\QueryBuilder;

final class SetFilter extends EnumFilter {
    public function getDescription(string $resourceClass): array {
        $description = [];
        $metadata = $this->getClassMetadata($resourceClass);
        $reflClass = $metadata->getReflectionClass();
        foreach ($this->properties as $property => $strategy) {
            $description["{$property}[]"] = [
                'is_collection' => true,
                'property' => $property,
                'required' => false,
                'schema' => [
                    'items' => [
                        'enum' => $this->getEnum(self::getReflectionProperty($reflClass, $property)),
                        'type' => $this->getType($metadata->getTypeOfField($property))
                    ],
                    'type' => 'array'
                ],
                'type' => 'array'
            ];
        }
        return $description;
    }

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?string $operationName = null): void {
        if (!$this->isPropertyEnabled($property, $resourceClass) || !$this->isPropertyMapped($property, $resourceClass)) {
            return;
        }

        $parameter = $queryNameGenerator->generateParameterName($property);
        $queryBuilder
            ->andWhere("{$queryBuilder->getRootAliases()[0]}.$property IN (:$parameter)")
            ->setParameter($parameter, $value, Connection::PARAM_STR_ARRAY);
    }
}
