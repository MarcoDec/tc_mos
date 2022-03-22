<?php

namespace App\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Symfony\Component\Validator\Constraints\Choice;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
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
                'schema' => ['$ref' => "#/components/schemas/{$this->getEnum($reflClass->getProperty($property))}"]
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

    private function getEnum(ReflectionProperty $property): string {
        foreach ($property->getAttributes(Choice::class) as $attribute) {
            /** @var Choice $choices */
            $choices = $attribute->newInstance();
            return $choices->name;
        }
        throw new InvalidArgumentException('Missing choice constraint.');
    }
}
