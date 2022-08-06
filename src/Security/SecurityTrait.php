<?php

namespace App\Security;

use App\Entity\Hr\Employee\Employee;
use Symfony\Component\Security\Core\Security;
use UnexpectedValueException;

trait SecurityTrait {
    public function __construct(private readonly Security $security) {
    }

    private function getUser(): Employee {
        $user = $this->security->getUser();
        if ($user instanceof Employee) {
            return $user;
        }
        throw new UnexpectedValueException(sprintf('Expected argument of type "%s", "%s" given.', Employee::class, get_debug_type($user)));
    }

    private function getUserId(): int {
        $id = $this->getUser()->getId();
        if (empty($id)) {
            throw new UnexpectedValueException(sprintf('Expected argument of type int, "%s" given.', get_debug_type($id)));
        }
        return $id;
    }
}
