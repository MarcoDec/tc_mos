<?php

namespace App\Entity\Interfaces;

interface WorkflowInterface {
    public function getId(): ?int;

    public function isDeletable(): bool;

    public function isFrozen(): bool;
}
