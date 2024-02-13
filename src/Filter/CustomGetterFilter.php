<?php
namespace App\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use http\Exception\InvalidArgumentException;

final class CustomGetterFilter extends AbstractFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if (!isset($this->properties[$property])) {
            return;
        }
        $parameters = $this->properties[$property];
        $fields = $parameters['fields'] ?? [];
        if (empty($fields)) {
            throw new InvalidArgumentException("fields must be defined to filter the getter $property");
        }

        // Appliquer la logique de filtrage basée sur le getter

        $alias = $queryBuilder->getRootAliases()[0];
        $orX = $queryBuilder->expr()->orX();
        foreach ($fields as $index => $field) {
            $paramName = sprintf('valeur_%s', $index);
            $orX->add($queryBuilder->expr()->like("$alias.$field", ":$paramName"));
            $queryBuilder->setParameter($paramName, '%'.$value.'%');
        }
        $queryBuilder->andWhere($orX);
    }
    public function getDescription(string $resourceClass): array
    {
        return [
            'getterFilter' => [
                'property' => 'getterFilter',
                'type' => 'string',
                'required' => false,
                'description' => 'Recherche partielle sur les champs spécifiés. Utilise la condition LIKE SQL pour filtrer les ressources en fonction de la correspondance partielle avec la valeur fournie.',
                'swagger' => [
                    'name' => 'getterFilter',
                    'type' => 'string',
                    'in' => 'query',
                    'description' => 'Recherche partielle sur les champs spécifiés par le paramètre fields du filtre. Par exemple, pour filtrer par nom ou prénom, spécifiez les champs correspondants dans les paramètres du filtre lors de la déclaration.',
                ],
            ],
        ];
    }

}
