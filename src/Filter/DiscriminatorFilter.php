<?php
declare(strict_types=1);

namespace App\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use function array_filter;
use function array_keys;
use function array_values;
use function in_array;
use function sprintf;

final class DiscriminatorFilter extends AbstractContextAwareFilter
{
   const PARAMETER_DISCRIMINATOR = 'type';


   public function getDescription(string $resourceClass): array
   {
      return [
         sprintf('%s[]', self::PARAMETER_DISCRIMINATOR) => [
            'property' => self::PARAMETER_DISCRIMINATOR,
            'type' => 'string',
            'required' => false,
            'openapi' => ['description' => 'Type of the Doctrine entity in the Inheritance Map (discriminator)'],
            'is_collection' => true,
         ],
      ];
   }
   /**
    * {@inheritdoc}
    */
   protected function filterProperty(
      string $property,
             $value,
      QueryBuilder $queryBuilder,
      QueryNameGeneratorInterface $queryNameGenerator,
      string $resourceClass,
      string $operationName = null,
      array $context = []
   )
   {
      if ($property !== self::PARAMETER_DISCRIMINATOR) {
         return;
      }
      $values = $this->normalizeValues((array) $value);
      if ($values === []) {
         return;
      }
      /** @var ClassMetadataInfo $metadata */
      $metadata = $this->managerRegistry->getManager()->getClassMetadata($resourceClass);
      /** @var array<string, string> $discriminatorMap */
      $discriminatorMap = $metadata->discriminatorMap ?? [];
      if ($discriminatorMap === []) {
         return;
      }
      $availableDiscriminators = array_keys($discriminatorMap);
      $validDiscriminatorValues = array_filter(
         $values,
         fn (string $discriminatorFromRequest): bool => in_array($discriminatorFromRequest, $availableDiscriminators, true)
      );
      if ($validDiscriminatorValues === []) {
         return;
      }

      $orX = $queryBuilder->expr()->orX();
      $alias = $queryBuilder->getRootAliases()[0];
      foreach ($validDiscriminatorValues as $validDiscriminatorValue) {
         $orX->add(
            $queryBuilder->expr()->isInstanceOf($alias, $discriminatorMap[$validDiscriminatorValue])
         );
      }
      $queryBuilder->andWhere($orX);
   }
   protected function normalizeValues(array $values): ?array
   {
      foreach ($values as $key => $value) {
         if (!\is_int($key) || !\is_string($value)) {
            unset($values[$key]);
         }
      }
      return array_values($values);
   }
}