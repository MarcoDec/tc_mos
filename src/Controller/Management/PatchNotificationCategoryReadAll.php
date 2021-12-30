<?php

namespace App\Controller\Management;

use App\Entity\Hr\Employee\Employee;
use App\Entity\Management\Notification;
use App\Repository\Management\NotificationRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class PatchNotificationCategoryReadAll extends AbstractController {
    public function __construct(private NotificationRepository $repo) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(string $category): array {
        /** @var Employee $employee */
        $employee = $this->getUser();
        /** @var Notification $notification */
        $notifications = $this->repo->findBy([
            'category' => $category,
            'user' => $employee->getId()
        ]);
        /** @var Notification $notification */
        foreach ($notifications as $notification) {
            $notification->setRead(true);
        }

        $this->repo->persistAll($notifications);
        return $notifications;
    }
}
