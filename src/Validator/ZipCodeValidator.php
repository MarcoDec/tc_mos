<?php

namespace App\Validator;

use App\Validator\ZipCode as ZipCodeAttribute;
use InvalidArgumentException;
use IsoCodes\ZipCode;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class ZipCodeValidator extends CountryValidator {
    public function validate(mixed $value, Constraint $constraint): void {
        if (!$constraint instanceof ZipCodeAttribute) {
            throw new UnexpectedTypeException($constraint, ZipCodeAttribute::class);
        }

        if (empty($value)) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (Countries::exists($country = $this->getCountry())) {
            try {
                if (!ZipCode::validate($value, $country)) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameters([
                            '{{ code }}' => $value,
                            '{{ country }}' => $country
                        ])
                        ->addViolation();
                }
            } catch (InvalidArgumentException) {
            }
        }
    }
}
