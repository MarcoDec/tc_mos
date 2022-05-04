<?php

namespace App\Fixtures;

/**
 * @phpstan-type PropertyConfigArray array{country?: bool, customscode?: bool, force_value?: string, new?: bool, new_name: string, new_ref?: class-string, old_ref?: string}
 */
final class PropertyConfig {
    private readonly bool $country;
    private readonly bool $customscode;
    private readonly ?string $forceValue;
    private readonly bool $new;
    private readonly string $newName;
    private readonly ?string $oldRef;

    /**
     * @param PropertyConfigArray $config
     */
    public function __construct(array $config) {
        $this->country = $config['country'] ?? false;
        $this->customscode = $config['customscode'] ?? false;
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

    public function isCustomscode(): bool {
        return $this->customscode;
    }

    public function isNew(): bool {
        return $this->new;
    }
}
