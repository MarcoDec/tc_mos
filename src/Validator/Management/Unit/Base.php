<?php

namespace App\Validator\Management\Unit;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
final class Base extends Constraint {
    public string $message = 'Une base sans parent ne peut être qu\'à 1.';

    public function getTargets(): string {
        return self::CLASS_CONSTRAINT;
    }
}
