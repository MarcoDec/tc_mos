<?php

namespace App\Service;

use App\Entity\Hr\Employee\Employee;
use App\Repository\Hr\TimeClock\ClockingRepository;

class ClockingsManager
{
   public function __construct(
      private readonly ClockingRepository $repo
   ) {}

   public function isEnter(Employee $employee):bool {
      $previousClocking = $this->repo->getPreviousClocking($employee);
      if ($previousClocking == null) {
         return True;
      }
      return !$previousClocking->isEnter();
   }
}