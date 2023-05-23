<?php

namespace App\Entity\Traits;

trait BarCodeTrait {
    public function getBarCode(): string {
        return static::getBarCodeTableNumber()."-{$this->getId()}";
    }
}
