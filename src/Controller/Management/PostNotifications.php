<?php

namespace App\Controller\Management;

use App\Entity\Hr\Employee\Employee;
use App\Entity\Management\Notification;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class PostNotifications extends AbstractController {
    /**
     * @throws Exception
     */
    public function __invoke(Notification $data): Notification {
        /** @var Employee $employee */
        $employee = $this->getUser();
        $data->setUser($employee);
        return $data;
    }
}
