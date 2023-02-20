<?php

namespace App\Controller\Hr\Employee;

use App\Entity\Hr\TimeClock\Clocking;
use App\Service\ClockingsManager;
use DateTime;

class ClockingController
{
   public function __construct(private ClockingsManager $manager) {
   }

   public function __invoke(Clocking $data): Clocking {
      $employee = $data->getEmployee();
      $data->setEnter($this->manager->isEnter($employee));
      return $data;
   }

}