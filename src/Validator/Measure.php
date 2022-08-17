<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
final class Measure extends Constraint {
    public string $message = 'Unité incorrecte. Elle doit appartenir à la famille des « {{ unit }} ».';
    public bool $positive = true;
    public string $positiveMessage = 'La valeur doit être positive';
}
