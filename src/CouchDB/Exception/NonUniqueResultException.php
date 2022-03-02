<?php

namespace App\CouchDB\Exception;

use Doctrine\ORM\NonUniqueResultException as DoctrineNonUniqueResultException;
use Exception;
use JetBrains\PhpStorm\Pure;

final class NonUniqueResultException extends Exception {
    #[Pure]
    public function __construct() {
        parent::__construct(DoctrineNonUniqueResultException::DEFAULT_MESSAGE);
    }
}
