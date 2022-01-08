<?php

namespace App\Controller\Management;

use App\Entity\Hr\Employee\Employee;
use App\Entity\Management\Notification;
use App\Repository\Management\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class DeleteNotificationCategoryAll extends AbstractController {
    public function __construct(private NotificationRepository $repo) {
    }

    /**
     * @return array<Notification>
     */
    public function __invoke(string $category): array {
        /** @var Employee $employee */
        $employee = $this->getUser();
        /** @var array<Notification> $notifications */
        $notifications = $this->repo->findBy([
            'category' => $category,
            'user' => $employee->getId()
        ]);
        $this->repo->removeAll($notifications);
        return $notifications;
    }
}
