<?php

namespace App\Controller\Hr\Employee;

use App\Doctrine\DBAL\Types\Embeddable\Hr\Event\EventStateType;

function optionalize(int $key, string $item):array {
    return [
        'id' => $key+1,
        'code' => $item,
        'text' => $item
    ];
}

class EmployeeStateTypeController
{
   public function __invoke(): array {
       return $this->normalizeEventStateType();
   }

   function normalizeEventStateType():array {
       $normalizedArray = [];
       $key = 0;
       foreach (EventStateType::TYPES as $stateType) {
           $normalizedArray[] = [
               'id' => ++$key,
               'code' => $stateType,
               'text' => $stateType
           ];
       }
       return $normalizedArray;
   }
}
