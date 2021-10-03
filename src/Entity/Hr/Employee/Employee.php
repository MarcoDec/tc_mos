<?php

namespace App\Entity\Hr\Employee;

use App\Entity\Traits\NameTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Employee extends User {
    use NameTrait;
}
