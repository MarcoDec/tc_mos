<?php

namespace App\Entity\Interfaces;

interface EmbeddedInterface {
    /**
     * Returns the list of embedded Mesures.
     *
     * @return mixed[]
     */
    public function getEmbeddedMeasures(): array;
}