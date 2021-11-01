<?php

namespace App\Entity\Hr\Employee;

use App\Entity\Security\User;
use App\Entity\Traits\NameTrait;
use App\Repository\Hr\Employee\EmployeeRepository;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
class Employee extends User {
    use NameTrait;
    #[Pure] public function __construct()
    {
       dump(['employee:construct before user']);
       parent::__construct();
       dump(['employee:construct after user']);
    }
}
