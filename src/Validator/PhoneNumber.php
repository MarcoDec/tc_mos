<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
final class PhoneNumber extends Constraint {
    public string $message = 'Le numéro {{ phoneNumber }} n\'est pas un numéro de téléphone valide pour le pays {{ country }}.';
}
