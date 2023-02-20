<?php

namespace App\Entity\Interfaces;

interface WorkflowInterface {
    public function getId(): ?int;

    public function getState(): ?string;

    public function isDeletable(): bool;

    public function isFrozen(): bool;

    public function setState(?string $state): self;
}
