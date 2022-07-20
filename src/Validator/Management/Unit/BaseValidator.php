<?php

namespace App\Validator\Management\Unit;

use App\Entity\Management\Unit;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class BaseValidator extends ConstraintValidator {
    public function validate(mixed $value, Constraint $constraint): void {
        if (!($constraint instanceof Base)) {
            throw new UnexpectedTypeException($constraint, Base::class);
        }

        if (empty($value)) {
            return;
        }

        if (!($value instanceof Unit)) {
            throw new UnexpectedValueException($value, Unit::class);
        }

        if ($value->getBase() !== 1.0 && $value->getParent() === null) {
            $this->context->buildViolation($constraint->message)
                ->atPath('base')
                ->addViolation();
        }
    }
}
