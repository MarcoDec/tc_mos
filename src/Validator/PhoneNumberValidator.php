<?php

namespace App\Validator;

use App\Validator\PhoneNumber as PhoneNumberAttribute;
use IsoCodes\PhoneNumber;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class PhoneNumberValidator extends CountryValidator {
    public function validate($value, Constraint $constraint): void {
        if (!$constraint instanceof PhoneNumberAttribute) {
            throw new UnexpectedTypeException($constraint, PhoneNumberAttribute::class);
        }

        if (empty($value)) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (!PhoneNumber::validate($value, $country = $this->getCountry())) {
            $this->context->buildViolation($constraint->message)
                ->setParameters([
                    '{{ phoneNumber }}' => $value,
                    '{{ country }}' => $country
                ])
                ->addViolation();
        }
    }
}
