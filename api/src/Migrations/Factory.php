<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Version\MigrationFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class Factory implements MigrationFactory {
    public function __construct(
        private readonly MigrationFactory $wrapped,
        private readonly UserPasswordHasherInterface $hasher
    ) {
    }

    public function createVersion(string $migrationClassName): AbstractMigration {
        $instance = $this->wrapped->createVersion($migrationClassName);
        if (method_exists($instance, 'setHasher')) {
            $instance->setHasher($this->hasher);
        }
        return $instance;
    }
}
