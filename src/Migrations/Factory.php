<?php

namespace App\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Version\MigrationFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class Factory implements MigrationFactory {
    public function __construct(
        private readonly MigrationFactory $decorated,
        private readonly UserPasswordHasherInterface $hasher
    ) {
    }

    public function createVersion(string $migrationClassName): AbstractMigration {
        $instance = $this->decorated->createVersion($migrationClassName);
        if (method_exists($instance, 'setHasher')) {
            $instance->setHasher($this->hasher);
        }
        return $instance;
    }
}
