<?php

namespace App\Validator;

use App\Validator\ZipCode as ZipCodeAttribute;
use IsoCodes\ZipCode;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilder;

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

        try {
            $valide = ZipCode::validate($value, $country = $this->getCountry());
        } catch (UnexpectedValueException) {
            (new ConstraintViolationBuilder(
                violations: $this->context->getViolations(),
                constraint: $constraint,
                message: (new NotBlank())->message,
                parameters: [],
                root: $this->context->getRoot(),
                propertyPath: 'address.country',
                invalidValue: $value,
                translator: $this->context->translator,
                translationDomain: $this->context->translationDomain
            ))->addViolation();
            return;
        }

        if (!$valide) {
            $this->context->buildViolation($constraint->message)
                ->setParameters([
                    '{{ code }}' => $value,
                    '{{ country }}' => $country
                ])
                ->addViolation();
        }
    }
}
