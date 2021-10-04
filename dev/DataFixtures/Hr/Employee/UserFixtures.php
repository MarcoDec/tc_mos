<?php

namespace App\DataFixtures\Hr\Employee;

use App\Entity\Hr\Employee\Employee;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFixtures extends Fixture {
    public function __construct(private UserPasswordHasherInterface $hasher) {
    }

    public function load(ObjectManager $manager): void {
        $manager->persist(
            ($user = new Employee())
                ->setName('Super')
                ->setPassword($this->hasher->hashPassword($user, 'super'))
                ->setUsername('super')
        );
        $manager->flush();
    }
}
