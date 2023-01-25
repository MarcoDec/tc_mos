<?php

namespace App\Fixtures;

final class PropertyConfig {
    private bool $country;
    private ?string $forceValue;
    private bool $new;
    private string $newName;
    private ?string $oldRef;

    /**
     * @param array{force_value?: string, new?: bool, new_name: string, new_ref?: class-string, old_ref?: string} $config
     */
    public function __construct(private string $name, array $config) {
        $this->country = $config['country'] ?? false;
        $this->forceValue = $config['force_value'] ?? null;
        $this->new = $config['new'] ?? false;
        $this->newName = $config['new_name'];
        $this->oldRef = $config['old_ref'] ?? null;
    }

    public function getForceValue(): ?string {
        return $this->forceValue;
    }

    public function getNewName(): string {
        return $this->newName;
    }

    public function getOldRef(): ?string {
        return $this->oldRef;
    }

    public function isCountry(): bool {
        return $this->country;
    }

    public function isNew(): bool {
        return $this->new;
    }
}
