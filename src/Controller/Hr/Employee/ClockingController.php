<?php

namespace App\Controller\Hr\Employee;

use App\Controller\File\FileUploadController;
use App\Entity\AbstractAttachment;
use App\Entity\Hr\TimeClock\Clocking;
use App\Filesystem\FileManager;
use App\Service\ClockingsManager;
use App\Service\ParameterManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ClockingController extends FileUploadController
{
   public function __construct(private EntityManagerInterface $entityManager, private FileManager $fileManager, private ParameterManager $parameterManager, private ClockingsManager $manager) {
      parent::__construct($entityManager,$fileManager,$parameterManager);
   }

   public function __invoke(Request $request/*Clocking $data*/): Clocking {
      parent::__invoke($request);
      /** @var AbstractAttachment $entity */
      $data= $request->attributes->get('data');
      $employee = $data->getEmployee();
      $data->setEnter($this->manager->isEnter($employee));

      return $data;
   }
   public function performCustomEntityAction(): void
   {
      $employee = $this->entity->getEmployee();
      $this->entity->setEnter($this->manager->isEnter($employee));
   }
}