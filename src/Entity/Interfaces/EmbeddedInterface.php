<?php

namespace App\Entity\Interfaces;

use App\Entity\Embeddable\Measure;

interface EmbeddedInterface {
    /**
     * Returns the list of embedded Mesures.
     *
     * @return Measure[]
     */
    public function getEmbeddedMeasures(): array;
}
