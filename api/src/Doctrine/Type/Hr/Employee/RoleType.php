<?php

declare(strict_types=1);

namespace App\Doctrine\Type\Hr\Employee;

use App\Doctrine\Type\SetType;

/** @extends SetType<Role> */
class RoleType extends SetType {
    public function getName(): string {
        return 'role';
    }

    /** @param Role $type */
    protected function convertToDatabaseValueEnum(mixed $type): string {
        return $type->value;
    }

    protected function convertToPHPValueEnum(string $type): Role {
        return Role::from($type);
    }

    /** @return string[] */
    protected function getEnumValues(): array {
        return Role::values();
    }
}
