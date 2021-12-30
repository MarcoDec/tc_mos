<?php

namespace App\Controller\Management;

use App\Entity\Hr\Employee\Employee;
use App\Entity\Management\Notification;
use App\Repository\Management\NotificationRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetNotifications extends AbstractController {
    public function __construct(private NotificationRepository $repo) {
    }

    /**
     * @throws Exception
     *
     * @return array<Notification>
     */
    public function __invoke(): array {
        /** @var Employee $employee */
        $employee = $this->getUser();
        return $this->repo->findBy([
            'user' => $employee->getId(),
            'read' => false
        ]);
    }
}
