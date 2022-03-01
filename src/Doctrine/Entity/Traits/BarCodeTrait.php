<?php

namespace App\Doctrine\Entity\Traits;

trait BarCodeTrait {
    final public function getBarCode(): string {
        return static::getBarCodeTableNumber()."-{$this->getId()}";
    }
}
