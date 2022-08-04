<?php

namespace App\Validator\Purchase\Component;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
final class ComponentAttribute extends Constraint {
    public string $message = 'La valeur de cet attribut doit être {{ type }}.';

    public function getTargets() {
        return self::CLASS_CONSTRAINT;
    }
}
