<?php

namespace App\Symfony\Component\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraints\Choice as ValidatorChoice;

#[Attribute]
final class Choice extends ValidatorChoice {
    /**
     * @param array<string> $choices
     * @param null|string[] $groups
     */
    public function __construct(array $choices, public readonly string $name, ?array $groups = null) {
        parent::__construct(choices: $choices, groups: $groups);
    }
}
