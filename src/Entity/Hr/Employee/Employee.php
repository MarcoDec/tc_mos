<?php

namespace App\Entity\Hr\Employee;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Security\User;
use App\Entity\Traits\NameTrait;
use App\Repository\Hr\Employee\EmployeeRepository;
use Doctrine\ORM\Mapping as ORM;

#[
   ORM\Entity(repositoryClass: EmployeeRepository::class),
   ApiResource(
      collectionOperations: [],
      itemOperations: ['get' => ['openapi_context' => [
         'description' => 'Récupère un utilisateur selon son id',
         'summary' => 'Récupère un utilisateur',
      ]]],
      normalizationContext: ['groups' => ['read:user'], 'openapi_definition_name' => 'User-read'])
]
class Employee extends User {
    use NameTrait;
}
