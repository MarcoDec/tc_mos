<?php

namespace App\Controller\Management;

use App\Entity\Hr\Employee\Employee;
use App\Repository\Management\NotificationRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetNotifications extends AbstractController
{
   public function __construct(private NotificationRepository $repo)
{
}

   /**
    * @throws Exception
    */
   public function __invoke(): array {
      /** @var Employee $employee */
      $employee = $this->getUser();
      $unReadNotifications = $this->repo->findBy([
         'user'=> $employee->getId(),
         'read'=> false
      ]);
      return $unReadNotifications;
   }
}