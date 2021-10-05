<?php

namespace App\Twig\Exception;

use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;

final class ViteEntryNotFoundException extends InvalidArgumentException {
    #[Pure]
    public function __construct(string $entry) {
        parent::__construct("The \"$entry\" entry was not found.");
    }
}
