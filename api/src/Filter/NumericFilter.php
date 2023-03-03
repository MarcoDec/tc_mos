<?php

declare(strict_types=1);

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\NumericFilter as OrmNumericFilter;

class NumericFilter extends OrmNumericFilter {
    protected function isNumericField(string $property, string $resourceClass): bool {
        if (parent::isNumericField($property, $resourceClass)) {
            return true;
        }
        return $this->getDoctrineFieldType($property, $resourceClass) === 'tinyint';
    }
}
