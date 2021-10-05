<?php

namespace App\Twig\Exception;

use JetBrains\PhpStorm\Pure;
use LogicException;

final class ViteMissingManifest extends LogicException {
    #[Pure]
    public function __construct() {
        parent::__construct('Missing manifest.json. Did you compile assets?');
    }
}
