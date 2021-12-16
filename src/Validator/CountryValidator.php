<?php

namespace App\Validator;

use App\Entity\Embeddable\Address;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

abstract class CountryValidator extends ConstraintValidator {
    final protected function getAddress(): Address {
        if (($object = $this->context->getObject()) instanceof Address) {
            return $object;
        }
        throw new UnexpectedValueException($object, Address::class);
    }

    final protected function getCountry(): string {
        if (!empty($country = $this->getAddress()->getCountry())) {
            return $country;
        }
        throw new UnexpectedValueException($country, 'string');
    }
}
