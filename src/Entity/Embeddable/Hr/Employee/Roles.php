<?php

namespace App\Entity\Embeddable\Hr\Employee;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Roles {
    // Comptabilité
    final public const ROLE_ACCOUNTING_ADMIN = 'ROLE_ACCOUNTING_ADMIN';
    final public const ROLE_ACCOUNTING_READER = 'ROLE_ACCOUNTING_READER';
    final public const ROLE_ACCOUNTING_WRITER = 'ROLE_ACCOUNTING_WRITER';

    // Hiérarchie
    final public const ROLE_HIERARCHY = [
        // Comptabilité
        self::ROLE_ACCOUNTING_READER => self::ROLE_USER,
        self::ROLE_ACCOUNTING_WRITER => self::ROLE_ACCOUNTING_READER,
        self::ROLE_ACCOUNTING_ADMIN => self::ROLE_ACCOUNTING_WRITER,
        // RH
        self::ROLE_HR_READER => self::ROLE_USER,
        self::ROLE_HR_WRITER => self::ROLE_HR_READER,
        self::ROLE_HR_ADMIN => self::ROLE_HR_WRITER,
        // Informatique
        self::ROLE_IT_ADMIN => self::ROLE_USER,
        // Niveaux
        self::ROLE_LEVEL_OPERATOR => self::ROLE_USER,
        self::ROLE_LEVEL_ANIMATOR => self::ROLE_LEVEL_OPERATOR,
        self::ROLE_LEVEL_MANAGER => self::ROLE_LEVEL_ANIMATOR,
        self::ROLE_LEVEL_DIRECTOR => self::ROLE_LEVEL_MANAGER,
        // Logistique
        self::ROLE_LOGISTICS_READER => self::ROLE_USER,
        self::ROLE_LOGISTICS_WRITER => self::ROLE_LOGISTICS_READER,
        self::ROLE_LOGISTICS_ADMIN => self::ROLE_LOGISTICS_WRITER,
        // Maintenance
        self::ROLE_MAINTENANCE_READER => self::ROLE_USER,
        self::ROLE_MAINTENANCE_WRITER => self::ROLE_MAINTENANCE_READER,
        self::ROLE_MAINTENANCE_ADMIN => self::ROLE_MAINTENANCE_WRITER,
        // Direction
        self::ROLE_MANAGEMENT_READER => self::ROLE_USER,
        self::ROLE_MANAGEMENT_WRITER => self::ROLE_MANAGEMENT_READER,
        self::ROLE_MANAGEMENT_ADMIN => self::ROLE_MANAGEMENT_WRITER,
        // Production
        self::ROLE_PRODUCTION_READER => self::ROLE_USER,
        self::ROLE_PRODUCTION_WRITER => self::ROLE_PRODUCTION_READER,
        self::ROLE_PRODUCTION_ADMIN => self::ROLE_PRODUCTION_WRITER,
        // Projet
        self::ROLE_PROJECT_READER => self::ROLE_USER,
        self::ROLE_PROJECT_WRITER => self::ROLE_PROJECT_READER,
        self::ROLE_PROJECT_ADMIN => self::ROLE_PROJECT_WRITER,
        // Achat
        self::ROLE_PURCHASE_READER => self::ROLE_USER,
        self::ROLE_PURCHASE_WRITER => self::ROLE_PURCHASE_READER,
        self::ROLE_PURCHASE_ADMIN => self::ROLE_PURCHASE_WRITER,
        // Qualité
        self::ROLE_QUALITY_READER => self::ROLE_USER,
        self::ROLE_QUALITY_WRITER => self::ROLE_QUALITY_READER,
        self::ROLE_QUALITY_ADMIN => self::ROLE_QUALITY_WRITER,
        // Ventes
        self::ROLE_SELLING_READER => self::ROLE_USER,
        self::ROLE_SELLING_WRITER => self::ROLE_SELLING_READER,
        self::ROLE_SELLING_ADMIN => self::ROLE_SELLING_WRITER
    ];

    // RH
    final public const ROLE_HR_ADMIN = 'ROLE_HR_ADMIN';
    final public const ROLE_HR_READER = 'ROLE_HR_READER';
    final public const ROLE_HR_WRITER = 'ROLE_HR_WRITER';

    // Informatique
    final public const ROLE_IT_ADMIN = 'ROLE_IT_ADMIN';

    // Niveaux
    final public const ROLE_LEVEL_ANIMATOR = 'ROLE_LEVEL_ANIMATOR';
    final public const ROLE_LEVEL_DIRECTOR = 'ROLE_LEVEL_DIRECTOR';
    final public const ROLE_LEVEL_MANAGER = 'ROLE_LEVEL_MANAGER';
    final public const ROLE_LEVEL_OPERATOR = 'ROLE_LEVEL_OPERATOR';

    // Logistique
    final public const ROLE_LOGISTICS_ADMIN = 'ROLE_LOGISTICS_ADMIN';
    final public const ROLE_LOGISTICS_READER = 'ROLE_LOGISTICS_READER';
    final public const ROLE_LOGISTICS_WRITER = 'ROLE_LOGISTICS_WRITER';

    // Maintenance
    final public const ROLE_MAINTENANCE_ADMIN = 'ROLE_MAINTENANCE_ADMIN';
    final public const ROLE_MAINTENANCE_READER = 'ROLE_MAINTENANCE_READER';
    final public const ROLE_MAINTENANCE_WRITER = 'ROLE_MAINTENANCE_WRITER';

    // Direction
    final public const ROLE_MANAGEMENT_ADMIN = 'ROLE_MANAGEMENT_ADMIN';
    final public const ROLE_MANAGEMENT_READER = 'ROLE_MANAGEMENT_READER';
    final public const ROLE_MANAGEMENT_WRITER = 'ROLE_MANAGEMENT_WRITER';

    // Production
    final public const ROLE_PRODUCTION_ADMIN = 'ROLE_PRODUCTION_ADMIN';
    final public const ROLE_PRODUCTION_READER = 'ROLE_PRODUCTION_READER';
    final public const ROLE_PRODUCTION_WRITER = 'ROLE_PRODUCTION_WRITER';

    // Projet
    final public const ROLE_PROJECT_ADMIN = 'ROLE_PROJECT_ADMIN';
    final public const ROLE_PROJECT_READER = 'ROLE_PROJECT_READER';
    final public const ROLE_PROJECT_WRITER = 'ROLE_PROJECT_WRITER';

    // Achat
    final public const ROLE_PURCHASE_ADMIN = 'ROLE_PURCHASE_ADMIN';
    final public const ROLE_PURCHASE_READER = 'ROLE_PURCHASE_READER';
    final public const ROLE_PURCHASE_WRITER = 'ROLE_PURCHASE_WRITER';

    // Qualité
    final public const ROLE_QUALITY_ADMIN = 'ROLE_QUALITY_ADMIN';
    final public const ROLE_QUALITY_READER = 'ROLE_QUALITY_READER';
    final public const ROLE_QUALITY_WRITER = 'ROLE_QUALITY_WRITER';

    // Ventes
    final public const ROLE_SELLING_ADMIN = 'ROLE_SELLING_ADMIN';
    final public const ROLE_SELLING_READER = 'ROLE_SELLING_READER';
    final public const ROLE_SELLING_WRITER = 'ROLE_SELLING_WRITER';

    // Utilisateur
    final public const ROLE_USER = 'ROLE_USER';

    /** @var string[] */
    #[ORM\Column(type: 'simple_array')]
    private array $roles = [self::ROLE_USER];

    final public function addRole(string $role): self {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
            sort($this->roles);
        }
        return $this;
    }

    /**
     * @return string[]
     */
    final public function getRoles(): array {
        return $this->roles;
    }

    final public function hasRole(string $role): bool {
        if (in_array($role, $this->roles)) {
            return true;
        }
        foreach (self::ROLE_HIERARCHY as $key => $value) {
            if ($role === $value && $this->hasRole($key)) {
                return true;
            }
        }
        return false;
    }

    final public function removeRole(string $role): self {
        if (false !== $key = array_search($role, $this->roles, true)) {
            array_splice($this->roles, (int) $key, 1);
        }
        return $this;
    }
}
