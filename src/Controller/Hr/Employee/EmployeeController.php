<?php

namespace App\Controller\Hr\Employee;

use App\Entity\Hr\Employee\Employee;
use App\Security\SecurityTrait;

final class EmployeeController {
    use SecurityTrait;

    public function __invoke(): Employee {
        return $this->getUser();
    }
}
