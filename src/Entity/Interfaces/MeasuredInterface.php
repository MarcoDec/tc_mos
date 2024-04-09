<?php

namespace App\Entity\Interfaces;

use App\Entity\Embeddable\Measure;
use App\Entity\Management\Unit;

interface MeasuredInterface {
    /**
     * @return Measure[]
     */
    public function getMeasures(): array;

    /**
     * @return Measure[]
     */
    public function getUnitMeasures(): array;

    /**
     * @return Measure[]
     */
    public function getCurrencyMeasures(): array;

    public function getUnit(): ?Unit;
}
