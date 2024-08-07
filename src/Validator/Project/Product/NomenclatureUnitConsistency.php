<?php

namespace App\Validator\Project\Product;

use Symfony\Component\Validator\Constraint;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class NomenclatureUnitConsistency extends Constraint
{
    public string $message = 'La quantité doit avoir la même unité que le composant.';

    /**
     * @return string|array|string[]
     */
    public function getTargets(): string|array
    {
        return [self::CLASS_CONSTRAINT];
    }
}