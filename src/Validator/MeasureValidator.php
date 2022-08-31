<?php

namespace App\Validator;

use App\Entity\Embeddable\Measure;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Unit;
use App\Validator\Measure as MeasureAttribute;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class MeasureValidator extends ConstraintValidator {
    public function validate(mixed $value, Constraint $constraint): void {
        if (!($constraint instanceof MeasureAttribute)) {
            throw new UnexpectedTypeException($constraint, MeasureAttribute::class);
        }

        if (empty($value)) {
            return;
        }

        if (!($value instanceof Measure)) {
            throw new UnexpectedValueException($value, Measure::class);
        }

        if (!$this->getUnit()->has($value->getUnit())) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ unit }}', (string) $this->getUnit()->getName())
                ->addViolation();
        }
        if ($constraint->positive && $value->getValue() < 0) {
            $this->context->buildViolation($constraint->positiveMessage)->addViolation();
        }
    }

    private function getObject(): MeasuredInterface {
        if (($object = $this->context->getObject()) instanceof MeasuredInterface) {
            return $object;
        }
        throw new UnexpectedValueException($object, MeasuredInterface::class);
    }

    private function getUnit(): Unit {
        if (!empty($unit = $this->getObject()->getUnit())) {
            return $unit;
        }
        dump($this->getObject(), $unit);
        throw new InvalidArgumentException();
    }
}
