<?php

namespace App\Doctrine\Entity\Interfaces;

use App\Doctrine\Entity\Embeddable\Measure;
use App\Doctrine\Entity\Management\Unit;

interface MeasuredInterface {
    /**
     * @return Measure[]
     */
    public function getMeasures(): array;

    public function getUnit(): ?Unit;
}
