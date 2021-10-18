<?php

namespace App\Fixtures;

final class PropertyConfig {
    private string $newName;

    /**
     * @param mixed[] $config
     */
    public function __construct(private string $name, array $config) {
        $this->newName = $config['new_name'];
    }

    public function getNewName(): string {
        return $this->newName;
    }
}
