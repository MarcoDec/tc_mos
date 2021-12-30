<?php

namespace App\Attribute\Couchdb\Abstract;

use JetBrains\PhpStorm\Pure;

abstract class Fetch {
    public const EAGER = 'EAGER';
    public const EXTRA_LAZY = 'EXTRA_LAZY';
    public const LAZY = 'LAZY';

    /**
     * @return string[]
     */
    public static function getAllFetchValues(): array {
        return [self::EAGER, self::EXTRA_LAZY, self::LAZY];
    }

    #[Pure]
   public static function isValidFetch(string $value): bool {
       return in_array($value, self::getAllFetchValues());
   }
}
