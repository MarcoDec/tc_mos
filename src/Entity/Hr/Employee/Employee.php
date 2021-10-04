<?php

namespace App\Entity\Hr\Employee;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Security\User;
use App\Entity\Traits\NameTrait;
use App\Repository\Hr\Employee\EmployeeRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ApiResource(
        collectionOperations: [],
        itemOperations: ['get' => ['openapi_context' => ['summary' => 'hidden']]],
        normalizationContext: ['groups' => ['read:name', 'read:user'], 'openapi_definition_name' => 'Employee-read']
    ),
    ORM\Entity(repositoryClass: EmployeeRepository::class)
]
class Employee extends User {
    use NameTrait;
}
