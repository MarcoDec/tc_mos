<?php

namespace App\Validator\Project\Product;

use App\Entity\Management\Unit;
use App\Entity\Purchase\Component\Component;
use App\Service\MeasureHydrator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Entity\Project\Product\Nomenclature;


class NomenclatureUnitConsistencyValidator extends ConstraintValidator
{
    public function __construct(private MeasureHydrator $hydrator)
    {
    }
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$value instanceof Nomenclature) {
            return;
        }
        if ($value->getComponent() === null) {
            return;
        }
        /** @var Component $hydratedComponent */
        $hydratedComponent = $this->hydrator->hydrateIn($value->getComponent());
        /** @var Nomenclature $hydratedNomenclature */
        $hydratedNomenclature = $this->hydrator->hydrateIn($value);
        /** @var Unit $componentUnit */
        $componentUnit = $hydratedComponent->getUnit();
        /** @var Unit $quantityUnit */
        $quantityUnit = $hydratedNomenclature->getQuantity()->getUnit();
        if (!$componentUnit->has($quantityUnit)) {
            $this->context->buildViolation($constraint->message)
                ->atPath('quantity')
                ->addViolation();
        }
    }
}