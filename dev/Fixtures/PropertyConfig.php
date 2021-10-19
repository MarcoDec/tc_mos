<?php

namespace App\Fixtures;

final class PropertyConfig {
    private string $newName;
    private ?string $oldRef;

    /**
     * @param array{new_name: string, new_ref?: class-string, old_ref?: string} $config
     */
    public function __construct(private string $name, array $config) {
        $this->newName = $config['new_name'];
        $this->oldRef = $config['old_ref'] ?? null;
    }

    public function getNewName(): string {
        return $this->newName;
    }

    public function getOldRef(): ?string {
        return $this->oldRef;
    }
}
