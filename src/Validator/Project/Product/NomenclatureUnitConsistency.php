<?php

namespace App\Validator\Project\Product;

use Symfony\Component\Validator\Constraint;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class NomenclatureUnitConsistency extends Constraint
{
    public string $message = 'La quantité doit avoir la même unité que le composant.';

    public function getTargets()
    {
        return [self::CLASS_CONSTRAINT];
    }
}