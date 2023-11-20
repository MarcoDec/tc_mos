<?php

namespace App\Validator\Project\Product;

use App\Entity\Management\Unit;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Entity\Project\Product\Nomenclature;


class NomenclatureUnitConsistencyValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$value instanceof Nomenclature) {
            return;
        }
        if ($value->getComponent() === null) {
            return;
        }
        /** @var Unit $componentUnit */
        $componentUnit = $value->getComponent()->getUnit();
        /** @var Unit $quantityUnit */
        $quantityUnit = $value->getQuantity()->getUnit();

        if (!$componentUnit->has($quantityUnit)) {
            $this->context->buildViolation($constraint->message)
                ->atPath('quantity')
                ->addViolation();
        }
    }
}