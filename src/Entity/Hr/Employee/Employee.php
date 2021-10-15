<?php

namespace App\Entity\Hr\Employee;

use App\Entity\Security\User;
use App\Entity\Traits\NameTrait;
use App\Repository\Hr\Employee\EmployeeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
class Employee extends User {
    use NameTrait;
}
