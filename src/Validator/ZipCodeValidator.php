<?php

namespace App\Validator;

use App\Entity\Embeddable\Address;
use App\Validator\ZipCode as ZipCodeAttribute;
use IsoCodes\ZipCode;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class ZipCodeValidator extends ConstraintValidator {
    public function validate($value, Constraint $constraint): void {
        if (!$constraint instanceof ZipCodeAttribute) {
            throw new UnexpectedTypeException($constraint, ZipCodeAttribute::class);
        }

        if (empty($value)) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (!ZipCode::validate($value, $country = $this->getCountry())) {
            $this->context->buildViolation($constraint->message)
                ->setParameters([
                    '{{ code }}' => $value,
                    '{{ country }}' => $country
                ])
                ->addViolation();
        }
    }

    private function getAddress(): Address {
        if (($object = $this->context->getObject()) instanceof Address) {
            return $object;
        }
        throw new UnexpectedValueException($object, Address::class);
    }

    private function getCountry(): string {
        if (!empty($country = $this->getAddress()->getCountry())) {
            return $country;
        }
        throw new UnexpectedValueException($country, 'string');
    }
}
