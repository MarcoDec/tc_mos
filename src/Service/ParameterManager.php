<?php

namespace App\Service;

use App\Entity\Management\Parameter;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class ParameterManager {
    public function __construct(private EntityManagerInterface $entityManager) {
    }

    public function getParameter(string $parameterClassName, string $parameterName): Parameter {
        if (!((new $parameterClassName()) instanceof Parameter)) {
            throw new Exception("la classe $parameterClassName n'étend pas de la classe ".Parameter::class);
        }
        dump(['$parameterClassName'=>$parameterClassName, '$parameterName'=>$parameterName]);
        /** @phpstan-ignore-next-line*/
        $repository = $this->entityManager->getRepository($parameterClassName);
        dump(['$repository'=>$repository]);
        return $repository->findOneBy([
            'name' => $parameterName
        ]);
    }
}
