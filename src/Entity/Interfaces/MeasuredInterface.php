<?php

namespace App\Entity\Interfaces;

use App\Entity\Embeddable\Measure;
use App\Entity\Management\Unit;

interface MeasuredInterface {
    /**
     * @return Measure[]
     */
    public function getMeasures(): array;

    public function getUnit(): ?Unit;
}
