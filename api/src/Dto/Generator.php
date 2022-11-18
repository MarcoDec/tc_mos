<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Entity;

interface Generator {
    public function generate(): Entity;
}
