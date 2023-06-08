<?php

namespace App\Security;

use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Hr\Employee\Employee;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserChecker implements UserCheckerInterface {
    public function checkPostAuth(UserInterface $user): void {
    }

    public function checkPreAuth(UserInterface $user): void {
        if (
            $user instanceof Employee
            && !(
                $user->hasRole(Roles::ROLE_LOGISTICS_WRITER)
                || $user->hasRole(Roles::ROLE_MAINTENANCE_WRITER)
                || $user->hasRole(Roles::ROLE_PRODUCTION_WRITER)
                || $user->hasRole(Roles::ROLE_QUALITY_WRITER)
            )
        ) {
            throw new CustomUserMessageAuthenticationException('L\'utilisateur n\'a pas les droits requis pour se connecter.');
        }
    }
}
