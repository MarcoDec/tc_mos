<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
final class ZipCode extends Constraint {
    public string $message = 'Le code {{ code }} n\'est pas un code postal valide pour le pays {{ country }}.';
}
