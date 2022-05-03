<?php

namespace App\DataFixtures\Hr\Employee;

use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Hr\Employee\Employee;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFixtures extends Fixture {
    public function __construct(private readonly UserPasswordHasherInterface $hasher) {
    }

    public function load(ObjectManager $manager): void {
        $manager->persist(
            ($user = new Employee())
                ->setName('Super')
                ->setPassword($this->hasher->hashPassword($user, 'super'))
                ->addRole(Roles::ROLE_ACCOUNTING_ADMIN)
                ->addRole(Roles::ROLE_HR_ADMIN)
                ->addRole(Roles::ROLE_IT_ADMIN)
                ->addRole(Roles::ROLE_LEVEL_DIRECTOR)
                ->addRole(Roles::ROLE_LOGISTICS_ADMIN)
                ->addRole(Roles::ROLE_MAINTENANCE_ADMIN)
                ->addRole(Roles::ROLE_MANAGEMENT_ADMIN)
                ->addRole(Roles::ROLE_PRODUCTION_ADMIN)
                ->addRole(Roles::ROLE_PROJECT_ADMIN)
                ->addRole(Roles::ROLE_PURCHASE_ADMIN)
                ->addRole(Roles::ROLE_QUALITY_ADMIN)
                ->addRole(Roles::ROLE_SELLING_ADMIN)
                ->setUsername('super')
        );
        $manager->flush();
    }
}
