<?php

namespace App\Service;

use App\Entity\Management\Parameter;
use Doctrine\ORM\EntityManagerInterface;

class ParameterManager
{
   public function __construct(private EntityManagerInterface $entityManager) {
   }

   public function getParameter(string $parameterClassName, string $parameterName):Parameter {
      if (!((new $parameterClassName()) instanceof Parameter)) throw new \Exception("la classe $parameterClassName n'étend pas de la classe ".Parameter::class);
      $repository = $this->entityManager->getRepository($parameterClassName);
      return $repository->findOneBy([
         'name' => $parameterName
      ]);
   }

}