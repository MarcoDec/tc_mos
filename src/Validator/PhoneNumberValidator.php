<?php

namespace App\Validator;

use App\Validator\PhoneNumber as PhoneNumberAttribute;
use InvalidArgumentException;
use IsoCodes\PhoneNumber;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class PhoneNumberValidator extends CountryValidator {
    public function validate(mixed $value, Constraint $constraint): void {
        if (!$constraint instanceof PhoneNumberAttribute) {
            throw new UnexpectedTypeException($constraint, PhoneNumberAttribute::class);
        }

        if (empty($value)) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (Countries::exists($country = $this->getCountry())) {
            try {
                if (!PhoneNumber::validate($value, $country)) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameters([
                            '{{ phoneNumber }}' => $value,
                            '{{ country }}' => $country
                        ])
                        ->addViolation();
                }
            } catch (InvalidArgumentException) {
            }
        }
    }
}
