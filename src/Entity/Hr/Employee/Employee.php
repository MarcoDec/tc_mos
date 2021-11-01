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

    #[Pure]
 public function __construct() {
     parent::__construct();
 }
}
